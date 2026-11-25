<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PricingItem;
use App\Models\ProjectMilestone;
use App\Models\Quotation;
use App\Services\InvoiceCalculator;
use App\Services\Settings;
use App\Support\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceCalculator $calculator,
        protected Options $options,
        protected Settings $settings,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('invoices.view');

        $invoices = Invoice::query()
            ->with(['client', 'project'])
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('search').'%';

                $query->where(fn (Builder $q) => $q->where('invoice_number', 'like', $term)
                    ->orWhereHas('client', fn (Builder $c) => $c->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)));
            })
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->input('status')))
            ->when($request->filled('client_id'), fn (Builder $q) => $q->where('client_id', $request->input('client_id')))
            ->when($request->filled('from'), fn (Builder $q) => $q->whereDate('invoice_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn (Builder $q) => $q->whereDate('invoice_date', '<=', $request->input('to')))
            ->when($request->boolean('overdue'), fn (Builder $q) => $q->unpaid()->whereDate('due_date', '<', today()))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totals = [
            'invoiced' => (float) Invoice::whereNotIn('status', ['draft', 'cancelled'])->sum('total'),
            'received' => (float) Invoice::whereNotIn('status', ['draft', 'cancelled'])->sum('paid_amount'),
            'outstanding' => (float) Invoice::unpaid()->sum('balance'),
        ];

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'clients' => $this->options->clients(),
            'totals' => $totals,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('invoices.create');

        $invoice = new Invoice([
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays((int) $this->settings->get('invoice_due_days', 15))->toDateString(),
            'currency' => $this->settings->currency(),
            'tax_percentage' => $this->settings->taxPercentage(),
            'discount_type' => 'percentage',
            'status' => Invoice::STATUS_DRAFT,
            'terms' => $this->settings->get('invoice_terms'),
            'client_id' => $request->integer('client_id') ?: null,
            'project_id' => $request->integer('project_id') ?: null,
        ]);

        // Raising an invoice straight from an approved quotation copies its lines.
        $prefillItems = [];

        if ($quotationId = $request->integer('quotation_id')) {
            $quotation = Quotation::with('items')->find($quotationId);

            if ($quotation) {
                $invoice->client_id = $quotation->client_id;
                $invoice->quotation_id = $quotation->id;
                $invoice->currency = $quotation->currency;
                $invoice->tax_percentage = $quotation->tax_percentage;
                $invoice->discount_type = $quotation->discount_type;
                $invoice->discount_value = $quotation->discount_value;

                $prefillItems = $quotation->items
                    ->where('is_free', false)
                    ->where('is_optional', false)
                    ->map(fn ($item) => [
                        'name' => $item->name,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ])
                    ->values()
                    ->all();
            }
        }

        return view('admin.invoices.create', $this->formData($invoice, $prefillItems));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('invoices.create');

        $data = $this->validateInvoice($request);

        $invoice = DB::transaction(function () use ($request, $data) {
            $invoice = Invoice::create($this->attributesFrom($data));

            $this->syncItems($invoice, $request);

            return $this->calculator->recalculate($invoice);
        });

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice '.$invoice->invoice_number.' created successfully.');
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('invoices.view');

        $invoice->load(['client', 'project', 'quotation', 'milestone', 'items', 'payments']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $this->authorize('invoices.edit');

        $invoice->load('items');

        return view('admin.invoices.edit', $this->formData($invoice));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('invoices.edit');

        $data = $this->validateInvoice($request);

        DB::transaction(function () use ($request, $invoice, $data) {
            $invoice->update($this->attributesFrom($data));

            $this->syncItems($invoice, $request);

            $this->calculator->recalculate($invoice);
        });

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('invoices.delete');

        if ($invoice->payments()->exists()) {
            return back()->with('error', 'This invoice has payments recorded against it. Delete those first.');
        }

        $invoice->delete();

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function print(Invoice $invoice): View
    {
        $this->authorize('invoices.view');

        $invoice->load(['client', 'project', 'items', 'payments']);

        return view('admin.invoices.print', [
            'invoice' => $invoice,
            'settings' => $this->settings,
        ]);
    }

    public function send(Invoice $invoice): RedirectResponse
    {
        $this->authorize('invoices.edit');

        $invoice->update(['status' => Invoice::STATUS_SENT, 'sent_at' => now()]);
        $this->calculator->recalculate($invoice);

        return back()->with('success', 'Invoice marked as sent.');
    }

    // ---------------------------------------------------------------- helpers

    /** @return array<string, mixed> */
    protected function formData(Invoice $invoice, array $prefillItems = []): array
    {
        return [
            'invoice' => $invoice,
            'prefillItems' => $prefillItems,
            'clients' => $this->options->clients(),
            'projects' => $this->options->projects(),
            'quotations' => $this->options->quotations(),
            'currencies' => $this->options->currencies(),
            'milestones' => ProjectMilestone::with('project')
                ->where('is_payment_milestone', true)
                ->get()
                ->mapWithKeys(fn (ProjectMilestone $milestone) => [
                    $milestone->id => ($milestone->project?->name ?? 'Project').' — '.$milestone->title,
                ])
                ->all(),
            'pricingItems' => PricingItem::active()->orderBy('name')->get(),
        ];
    }

    /** @return array<string, mixed> */
    protected function validateInvoice(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'quotation_id' => ['nullable', 'exists:quotations,id'],
            'milestone_id' => ['nullable', 'exists:project_milestones,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'currency' => ['required', 'string', 'size:3'],
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', Rule::in(array_keys(Invoice::STATUSES))],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.pricing_item_id' => ['nullable', 'exists:pricing_items,id'],
        ], [], ['items' => 'invoice items']);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function attributesFrom(array $validated): array
    {
        unset($validated['items']);

        return $validated;
    }

    protected function syncItems(Invoice $invoice, Request $request): void
    {
        $invoice->items()->delete();

        foreach ($request->input('items', []) as $index => $item) {
            if (blank($item['name'] ?? null)) {
                continue;
            }

            $invoice->items()->create([
                'pricing_item_id' => $item['pricing_item_id'] ?? null,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'sort_order' => $index,
            ]);
        }
    }
}
