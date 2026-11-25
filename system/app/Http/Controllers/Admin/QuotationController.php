<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingItem;
use App\Models\Quotation;
use App\Notifications\QuotationDecided;
use App\Services\ConversionService;
use App\Services\NotificationDispatcher;
use App\Services\QuotationCalculator;
use App\Services\Settings;
use App\Support\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Quotations carry dynamic line items in three sections (main modules,
 * additional services, free services), payment milestones and a full commercial
 * narrative. Totals are always recalculated from the stored items.
 */
class QuotationController extends Controller
{
    public function __construct(
        protected QuotationCalculator $calculator,
        protected Options $options,
        protected Settings $settings,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('quotations.view');

        $quotations = Quotation::query()
            ->with(['client', 'preparedBy', 'convertedProject'])
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('search').'%';

                $query->where(fn (Builder $q) => $q->where('quotation_number', 'like', $term)
                    ->orWhere('project_name', 'like', $term)
                    ->orWhereHas('client', fn (Builder $c) => $c->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)));
            })
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->input('status')))
            ->when($request->filled('client_id'), fn (Builder $q) => $q->where('client_id', $request->input('client_id')))
            ->when($request->filled('from'), fn (Builder $q) => $q->whereDate('quotation_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn (Builder $q) => $q->whereDate('quotation_date', '<=', $request->input('to')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.quotations.index', [
            'quotations' => $quotations,
            'clients' => $this->options->clients(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('quotations.create');

        $quotation = new Quotation([
            'quotation_date' => now()->toDateString(),
            'valid_until' => now()->addDays((int) $this->settings->get('quotation_validity_days', 30))->toDateString(),
            'currency' => $this->settings->currency(),
            'tax_percentage' => $this->settings->taxPercentage(),
            'discount_type' => 'percentage',
            'status' => Quotation::STATUS_DRAFT,
            'terms_conditions' => $this->settings->get('quotation_terms'),
            // Prefilled when arriving from a website inquiry.
            'client_id' => $request->integer('client_id') ?: null,
            'website_inquiry_id' => $request->integer('inquiry_id') ?: null,
            'project_name' => $request->string('project_name')->toString(),
        ]);

        return view('admin.quotations.create', $this->formData($quotation));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('quotations.create');

        $data = $this->attributesFrom($this->validateQuotation($request));

        $quotation = DB::transaction(function () use ($request, $data) {
            $quotation = Quotation::create($data);

            $this->syncItems($quotation, $request);
            $this->syncMilestones($quotation, $request);

            $this->calculator->recalculate($quotation);
            $this->calculator->syncMilestoneAmounts($quotation);

            // Close the loop back to the inquiry this quotation came from.
            if ($inquiry = $quotation->websiteInquiry) {
                $inquiry->forceFill(['converted_quotation_id' => $quotation->id])->save();
            }

            return $quotation;
        });

        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('success', 'Quotation '.$quotation->quotation_number.' created successfully.');
    }

    public function show(Quotation $quotation): View
    {
        $this->authorize('quotations.view');

        $quotation->load(['client', 'lead', 'preparedBy', 'items.pricingItem', 'milestones', 'revisions', 'parent', 'convertedProject', 'invoices']);

        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation): View
    {
        $this->authorize('quotations.edit');

        if (! $quotation->isEditable()) {
            return view('admin.quotations.show', [
                'quotation' => $quotation->load(['client', 'items', 'milestones']),
                'lockedNotice' => 'This quotation is '.strtolower(Quotation::STATUSES[$quotation->status])
                    .' and can no longer be edited. Create a revision to change it.',
            ]);
        }

        $quotation->load(['items', 'milestones']);

        return view('admin.quotations.edit', $this->formData($quotation));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('quotations.edit');

        abort_unless($quotation->isEditable(), 403, 'Approved or rejected quotations cannot be edited.');

        $data = $this->attributesFrom($this->validateQuotation($request));

        DB::transaction(function () use ($request, $quotation, $data) {
            $quotation->update($data);

            $this->syncItems($quotation, $request);
            $this->syncMilestones($quotation, $request);

            $this->calculator->recalculate($quotation);
            $this->calculator->syncMilestoneAmounts($quotation);
        });

        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorize('quotations.delete');

        $quotation->delete();

        return redirect()
            ->route('admin.quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    /** Client-facing A4 document. Internal cost and profit are never included. */
    public function print(Quotation $quotation): View
    {
        $this->authorize('quotations.view');

        $quotation->load(['client', 'preparedBy', 'items', 'milestones']);

        return view('admin.quotations.print', [
            'quotation' => $quotation,
            'settings' => $this->settings,
        ]);
    }

    public function duplicate(Quotation $quotation): RedirectResponse
    {
        $this->authorize('quotations.create');

        $copy = DB::transaction(function () use ($quotation) {
            $new = $quotation->replicate([
                'quotation_number', 'parent_id', 'revision', 'status', 'sent_at',
                'decided_at', 'converted_project_id', 'client_accepted_by',
                'client_accepted_date', 'client_remarks',
            ]);

            $new->fill([
                'status' => Quotation::STATUS_DRAFT,
                'quotation_date' => now()->toDateString(),
                'valid_until' => now()->addDays((int) $this->settings->get('quotation_validity_days', 30))->toDateString(),
                'project_name' => $quotation->project_name.' (copy)',
            ])->save();

            foreach ($quotation->items as $item) {
                $line = $item->replicate();
                $line->quotation_id = $new->id;
                $line->save();
            }

            foreach ($quotation->milestones as $milestone) {
                $stage = $milestone->replicate();
                $stage->quotation_id = $new->id;
                $stage->save();
            }

            return $this->calculator->recalculate($new);
        });

        return redirect()
            ->route('admin.quotations.edit', $copy)
            ->with('success', 'Quotation duplicated as '.$copy->quotation_number.'.');
    }

    /** Creates QT-2026-0001-R1 style revisions, leaving the original intact. */
    public function revise(Quotation $quotation, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('quotations.create');

        $revision = $conversions->reviseQuotation($quotation);

        return redirect()
            ->route('admin.quotations.edit', $revision)
            ->with('success', 'Revision '.$revision->quotation_number.' created.');
    }

    public function send(Quotation $quotation): RedirectResponse
    {
        $this->authorize('quotations.edit');

        $quotation->update([
            'status' => Quotation::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Quotation marked as sent.');
    }

    public function approve(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('quotations.approve');

        $data = $request->validate([
            'client_accepted_by' => ['nullable', 'string', 'max:255'],
            'client_accepted_date' => ['nullable', 'date'],
            'client_remarks' => ['nullable', 'string'],
        ]);

        $quotation->update($data + [
            'status' => Quotation::STATUS_APPROVED,
            'decided_at' => now(),
            'client_accepted_date' => $data['client_accepted_date'] ?? now()->toDateString(),
        ]);

        app(NotificationDispatcher::class)->toPermission('quotations.view', new QuotationDecided($quotation));

        return back()->with('success', 'Quotation approved. You can now convert it into a project.');
    }

    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('quotations.approve');

        $data = $request->validate([
            'client_remarks' => ['nullable', 'string'],
        ]);

        $quotation->update($data + [
            'status' => Quotation::STATUS_REJECTED,
            'decided_at' => now(),
        ]);

        app(NotificationDispatcher::class)->toPermission('quotations.view', new QuotationDecided($quotation));

        return back()->with('success', 'Quotation marked as rejected.');
    }

    public function convertToProject(Quotation $quotation, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('projects.create');

        if ($quotation->status !== Quotation::STATUS_APPROVED) {
            return back()->with('error', 'Only an approved quotation can be converted into a project.');
        }

        if ($quotation->converted_project_id) {
            return back()->with('error', 'This quotation is already linked to project '.$quotation->convertedProject->project_number.'.');
        }

        $project = $conversions->quotationToProject($quotation);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project '.$project->project_number.' created from this quotation.');
    }

    // ---------------------------------------------------------------- helpers

    /** @return array<string, mixed> */
    protected function formData(Quotation $quotation): array
    {
        return [
            'quotation' => $quotation,
            'clients' => $this->options->clients(),
            'leads' => $this->options->leads(),
            'employees' => $this->options->employees(),
            'currencies' => $this->options->currencies(),
            'pricingItems' => PricingItem::active()->with('category')->orderBy('name')->get(),
            'canSeeCost' => auth('admin')->user()?->hasPermission('quotations.view_cost') ?? false,
        ];
    }

    /** @return array<string, mixed> */
    protected function validateQuotation(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'website_inquiry_id' => ['nullable', 'exists:website_inquiries,id'],
            'project_name' => ['required', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:150'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'currency' => ['required', 'string', 'size:3'],
            'prepared_by' => ['nullable', 'exists:employees,id'],
            'status' => ['required', Rule::in(array_keys(Quotation::STATUSES))],

            'project_summary' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'scope' => ['nullable', 'string'],
            'proposed_solution' => ['nullable', 'string'],
            'technologies' => ['nullable', 'string', 'max:1000'],
            'project_timeline' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'revisions_included' => ['nullable', 'integer', 'min:0', 'max:255'],
            'support_period' => ['nullable', 'string', 'max:150'],
            'hosting_domain_info' => ['nullable', 'string'],
            'third_party_charges' => ['nullable', 'string'],
            'client_responsibilities' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],

            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.section' => ['required', Rule::in(array_keys(Quotation::SECTIONS))],
            'items.*.pricing_item_id' => ['nullable', 'exists:pricing_items,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.features' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.internal_cost' => ['nullable', 'numeric', 'min:0'],
            'items.*.estimated_days' => ['nullable', 'integer', 'min:0', 'max:3650'],

            'milestones' => ['nullable', 'array'],
            'milestones.*.title' => ['required_with:milestones.*.percentage', 'nullable', 'string', 'max:255'],
            'milestones.*.description' => ['nullable', 'string', 'max:500'],
            'milestones.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'milestones.*.due_condition' => ['nullable', 'string', 'max:255'],
        ], [], [
            'items' => 'quotation items',
        ]);
    }

    /**
     * Strips the nested collections and reshapes the comma separated
     * technologies input into the array the model casts.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function attributesFrom(array $validated): array
    {
        unset($validated['items'], $validated['milestones']);

        $validated['technologies'] = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($validated['technologies'] ?? '')),
        ))) ?: null;

        return $validated;
    }

    /**
     * Line items are replaced wholesale on every save. Flags arrive as separate
     * checkbox arrays because unchecked boxes are simply absent from the post.
     */
    protected function syncItems(Quotation $quotation, Request $request): void
    {
        $quotation->items()->delete();

        foreach ($request->input('items', []) as $index => $item) {
            if (blank($item['name'] ?? null)) {
                continue;
            }

            $quotation->items()->create([
                'pricing_item_id' => $item['pricing_item_id'] ?? null,
                'section' => $item['section'] ?? Quotation::SECTION_MAIN,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'features' => $item['features'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'internal_cost' => $item['internal_cost'] ?? null,
                'estimated_days' => $item['estimated_days'] ?? null,
                'is_optional' => (bool) ($item['is_optional'] ?? false),
                'is_free' => (bool) ($item['is_free'] ?? false),
                'sort_order' => $index,
            ]);
        }
    }

    protected function syncMilestones(Quotation $quotation, Request $request): void
    {
        $quotation->milestones()->delete();

        foreach ($request->input('milestones', []) as $index => $milestone) {
            if (blank($milestone['title'] ?? null)) {
                continue;
            }

            $quotation->milestones()->create([
                'title' => $milestone['title'],
                'description' => $milestone['description'] ?? null,
                'percentage' => $milestone['percentage'] ?? null,
                'amount' => 0,
                'due_condition' => $milestone['due_condition'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }
}
