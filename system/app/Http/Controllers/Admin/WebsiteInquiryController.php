<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quotation;
use App\Models\WebsiteInquiry;
use App\Services\ConversionService;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Inbox for every public form on the website. Also serves as the CMS
 * "Contact Messages" screen — there is no separate table for those.
 */
class WebsiteInquiryController extends AdminResourceController
{
    protected string $model = WebsiteInquiry::class;

    protected string $route = 'inquiries';

    protected string $permission = 'inquiries';

    protected string $singular = 'Inquiry';

    protected string $plural = 'Website Inquiries';

    protected string $description = 'Messages submitted through the public website contact and quote forms.';

    protected string $icon = 'fa-inbox';

    protected array $searchable = ['inquiry_number', 'name', 'email', 'phone', 'company', 'subject', 'message'];

    protected array $with = ['assignee', 'convertedLead', 'convertedClient'];

    protected bool $hasShow = true;

    protected function columns(): array
    {
        return [
            Column::code('inquiry_number', 'Inquiry #'),
            Column::custom('name', 'From', fn (WebsiteInquiry $inquiry) => '<div class="admin-cell-stack"><span>'
                .e($inquiry->name).'</span><small>'.e($inquiry->email ?: $inquiry->phone ?: '—').'</small></div>'),
            Column::text('service', 'Service'),
            Column::text('source_page', 'Page'),
            Column::badge('priority', 'Priority', WebsiteInquiry::PRIORITIES),
            Column::badge('status', 'Status', WebsiteInquiry::STATUSES),
            Column::relation('assignee.name', 'Assigned To'),
            Column::datetime('created_at', 'Received'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Contact'),
            Field::text('name', 'Name', ['required' => true]),
            Field::text('company', 'Company'),
            Field::email('email', 'Email'),
            Field::tel('phone', 'Phone'),
            Field::tel('whatsapp', 'WhatsApp'),

            Field::heading('Inquiry'),
            Field::text('service', 'Service Interested In'),
            Field::text('budget', 'Budget'),
            Field::text('subject', 'Subject'),
            Field::textarea('message', 'Message', ['rows' => 5]),
            Field::text('source_page', 'Source Page'),

            Field::heading('Handling'),
            Field::select('status', 'Status', WebsiteInquiry::STATUSES, ['required' => true]),
            Field::select('priority', 'Priority', WebsiteInquiry::PRIORITIES, ['required' => true]),
            Field::select('assigned_to', 'Assigned Employee', $this->options->employees()),
            Field::textarea('admin_notes', 'Internal Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => WebsiteInquiry::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => WebsiteInquiry::PRIORITIES],
            ['key' => 'assigned_to', 'label' => 'Assignee', 'options' => $this->options->employees()],
            ['key' => 'unread', 'label' => 'Read State', 'options' => ['1' => 'Unread only']],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'created_at'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'created_at'],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return $request->boolean('unread') ? $query->unread() : $query;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'service' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(WebsiteInquiry::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(WebsiteInquiry::PRIORITIES))],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'admin_notes' => ['nullable', 'string'],
        ];
    }

    public function show(WebsiteInquiry $inquiry): View
    {
        $this->authorizeAction('view');

        $inquiry->markAsRead();
        $inquiry->load(['assignee', 'convertedLead', 'convertedClient', 'convertedQuotation', 'convertedProject', 'followUps.assignee']);

        return view('admin.inquiries.show', [
            'inquiry' => $inquiry,
            'employees' => $this->options->employees(),
        ]);
    }

    public function markRead(WebsiteInquiry $inquiry): RedirectResponse
    {
        $this->authorizeAction('edit');

        $inquiry->markAsRead();

        return back()->with('success', 'Inquiry marked as read.');
    }

    public function assign(Request $request, WebsiteInquiry $inquiry): RedirectResponse
    {
        $this->authorizeAction('edit');

        $data = $request->validate([
            'assigned_to' => ['nullable', 'exists:employees,id'],
        ]);

        $inquiry->update($data);

        return back()->with('success', 'Inquiry assignment updated.');
    }

    public function convertToLead(WebsiteInquiry $inquiry, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('inquiries.convert');

        if ($inquiry->converted_lead_id) {
            return back()->with('error', 'This inquiry is already linked to lead '.$inquiry->convertedLead->lead_number.'.');
        }

        $lead = $conversions->inquiryToLead($inquiry);

        return redirect()
            ->route('admin.leads.show', $lead)
            ->with('success', 'Inquiry converted to lead '.$lead->lead_number.'.');
    }

    /** Converts through a lead first so the history chain stays intact. */
    public function convertToClient(WebsiteInquiry $inquiry, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('inquiries.convert');

        $client = DB::transaction(function () use ($inquiry, $conversions) {
            $lead = $inquiry->convertedLead ?? $conversions->inquiryToLead($inquiry);

            return $conversions->leadToClient($lead);
        });

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Inquiry converted to client successfully.');
    }

    /** Prefills a new quotation from the inquiry, creating the client first. */
    public function createQuotation(WebsiteInquiry $inquiry, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('quotations.create');

        $client = DB::transaction(function () use ($inquiry, $conversions) {
            if ($inquiry->converted_client_id) {
                return $inquiry->convertedClient;
            }

            $lead = $inquiry->convertedLead ?? $conversions->inquiryToLead($inquiry);

            return $lead->client ?? $conversions->leadToClient($lead);
        });

        return redirect()->route('admin.quotations.create', [
            'client_id' => $client->id,
            'inquiry_id' => $inquiry->id,
            'project_name' => $inquiry->service ?: $inquiry->subject,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorizeAction('view');

        $inquiries = $this->applyFilters($this->baseQuery(), $request)
            ->orderByDesc('id')
            ->get();

        return response()->streamDownload(function () use ($inquiries) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Inquiry #', 'Name', 'Company', 'Email', 'Phone', 'Service',
                'Budget', 'Subject', 'Message', 'Source Page', 'Status',
                'Priority', 'Assigned To', 'Received',
            ]);

            foreach ($inquiries as $inquiry) {
                fputcsv($handle, [
                    $inquiry->inquiry_number,
                    $inquiry->name,
                    $inquiry->company,
                    $inquiry->email,
                    $inquiry->phone,
                    $inquiry->service,
                    $inquiry->budget,
                    $inquiry->subject,
                    $inquiry->message,
                    $inquiry->source_page,
                    WebsiteInquiry::STATUSES[$inquiry->status] ?? $inquiry->status,
                    $inquiry->priority,
                    $inquiry->assignee?->name,
                    $inquiry->created_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'website-inquiries-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
