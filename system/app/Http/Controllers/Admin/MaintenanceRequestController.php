<?php

namespace App\Http\Controllers\Admin;

use App\Models\MaintenanceRequest;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceRequestController extends AdminResourceController
{
    protected string $model = MaintenanceRequest::class;

    protected string $route = 'maintenance-requests';

    protected string $permission = 'maintenance_requests';

    protected string $singular = 'Maintenance Request';

    protected string $plural = 'Maintenance Requests';

    protected string $description = 'Post-launch work requests, quoted and approved before delivery.';

    protected string $icon = 'fa-screwdriver-wrench';

    protected array $searchable = ['request_number', 'title', 'description'];

    protected array $with = ['client', 'project', 'assignee'];

    protected function columns(): array
    {
        return [
            Column::code('request_number', 'Request #'),
            Column::text('title', 'Title'),
            Column::relation('client.company_name', 'Client'),
            Column::relation('project.name', 'Project'),
            Column::badge('priority', 'Priority', MaintenanceRequest::PRIORITIES),
            Column::money('estimated_cost', 'Estimated'),
            Column::boolean('client_approved', 'Approved'),
            Column::badge('status', 'Status', MaintenanceRequest::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('project_id', 'Project', $this->options->projects()),
            Field::select('priority', 'Priority', MaintenanceRequest::PRIORITIES, ['required' => true]),
            Field::select('status', 'Status', MaintenanceRequest::STATUSES, ['required' => true]),
            Field::select('assigned_to', 'Assigned Employee', $this->options->employees()),
            Field::money('estimated_cost', 'Estimated Cost'),
            Field::money('approved_cost', 'Approved Cost'),
            Field::number('estimated_hours', 'Estimated Hours'),
            Field::date('client_approved_at', 'Client Approved On'),
            Field::checkbox('client_approved', 'Approved by client'),
            Field::textarea('description', 'Description', ['rows' => 5]),
            Field::textarea('notes', 'Internal Notes'),
            Field::file('attachment', 'Attachment', ['attachment' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => MaintenanceRequest::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => MaintenanceRequest::PRIORITIES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'priority' => ['required', Rule::in(array_keys(MaintenanceRequest::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(MaintenanceRequest::STATUSES))],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'approved_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_hours' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'client_approved' => ['boolean'],
            'client_approved_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'maintenance');
        }
    }
}
