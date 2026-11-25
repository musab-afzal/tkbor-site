<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Notifications\NewLeadCreated;
use App\Services\ConversionService;
use App\Services\NotificationDispatcher;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends AdminResourceController
{
    protected string $model = Lead::class;

    protected string $route = 'leads';

    protected string $permission = 'leads';

    protected string $singular = 'Lead';

    protected string $plural = 'Leads';

    protected string $description = 'Track every opportunity from first contact to won or lost.';

    protected string $icon = 'fa-bullseye';

    protected array $searchable = ['lead_number', 'name', 'company', 'email', 'phone'];

    protected array $with = ['assignee', 'client'];

    protected bool $hasShow = true;

    protected function columns(): array
    {
        return [
            Column::code('lead_number', 'Lead #'),
            Column::text('name', 'Name'),
            Column::text('company', 'Company'),
            Column::text('interested_service', 'Interested In'),
            Column::money('estimated_budget', 'Budget'),
            Column::badge('priority', 'Priority', Lead::PRIORITIES),
            Column::badge('status', 'Status', Lead::STATUSES),
            Column::relation('assignee.name', 'Assigned To'),
            Column::date('next_follow_up_at', 'Next Follow-up'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Contact'),
            Field::text('name', 'Full Name', ['required' => true]),
            Field::text('company', 'Company'),
            Field::email('email', 'Email'),
            Field::tel('phone', 'Phone'),
            Field::tel('whatsapp', 'WhatsApp'),
            Field::text('city', 'City'),
            Field::text('country', 'Country'),

            Field::heading('Opportunity'),
            Field::select('source', 'Source', Lead::SOURCES),
            Field::select('service_id', 'Linked Service', $this->options->services(), [
                'help' => 'Optional link to a service you already offer on the website.',
            ]),
            Field::text('interested_service', 'Interested Service'),
            Field::money('estimated_budget', 'Estimated Budget'),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::select('priority', 'Priority', Lead::PRIORITIES, ['required' => true]),
            Field::select('status', 'Status', Lead::STATUSES, ['required' => true]),
            Field::select('assigned_to', 'Assigned Employee', $this->options->employees()),
            Field::date('next_follow_up_at', 'Next Follow-up Date'),

            Field::heading('Notes'),
            Field::textarea('notes', 'Notes'),
            Field::textarea('lost_reason', 'Lost Reason', [
                'help' => 'Only used when the lead is marked as lost.',
            ]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Lead::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => Lead::PRIORITIES],
            ['key' => 'source', 'label' => 'Source', 'options' => Lead::SOURCES],
            ['key' => 'assigned_to', 'label' => 'Assignee', 'options' => $this->options->employees()],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'created_at'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'created_at'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'source' => ['nullable', Rule::in(array_keys(Lead::SOURCES))],
            'service_id' => ['nullable', 'exists:services,id'],
            'interested_service' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0', 'max:99999999999'],
            'currency' => ['nullable', 'string', 'size:3'],
            'priority' => ['required', Rule::in(array_keys(Lead::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'lost_reason' => ['nullable', 'string'],
        ];
    }

    /** Records status changes and assignment changes on the lead timeline. */
    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        /** @var Lead $record */
        if ($created) {
            $this->log($record, 'note', 'Lead created');

            app(NotificationDispatcher::class)->toEmployees([$record->assigned_to], new NewLeadCreated($record));

            return;
        }

        $changes = $record->getChanges();

        if (isset($changes['status'])) {
            LeadActivity::create([
                'lead_id' => $record->id,
                'admin_id' => Auth::guard('admin')->id(),
                'type' => 'status_change',
                'title' => 'Status changed to '.(Lead::STATUSES[$record->status] ?? $record->status),
                'old_status' => $record->getOriginal('status'),
                'new_status' => $record->status,
            ]);
        }

        if (isset($changes['assigned_to'])) {
            $this->log($record, 'assignment', 'Assigned to '.($record->assignee?->name ?? 'nobody'));

            app(NotificationDispatcher::class)->toEmployees([$record->assigned_to], new NewLeadCreated($record));
        }
    }

    public function show(Lead $lead): View
    {
        $this->authorizeAction('view');

        $lead->load([
            'assignee', 'client', 'service', 'websiteInquiry',
            'activities.admin', 'followUps.assignee', 'meetings', 'quotations', 'projects',
        ]);

        return view('admin.leads.show', compact('lead'));
    }

    /** Free-form note added from the lead detail page. */
    public function addActivity(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorizeAction('edit');

        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(LeadActivity::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $lead->activities()->create($data + ['admin_id' => Auth::guard('admin')->id()]);

        return back()->with('success', 'Activity added to the lead history.');
    }

    public function convertToClient(Lead $lead, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('leads.convert');

        if ($lead->isConverted()) {
            return back()->with('error', 'This lead is already linked to client '.$lead->client->displayName().'.');
        }

        $client = $conversions->leadToClient($lead);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Lead converted to client successfully.');
    }

    public function convertToProject(Lead $lead, ConversionService $conversions): RedirectResponse
    {
        $this->authorize('leads.convert');

        $project = $conversions->leadToProject($lead);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Lead converted to project '.$project->project_number.'.');
    }

    protected function log(Lead $lead, string $type, string $title): void
    {
        LeadActivity::create([
            'lead_id' => $lead->id,
            'admin_id' => Auth::guard('admin')->id(),
            'type' => $type,
            'title' => $title,
        ]);
    }
}
