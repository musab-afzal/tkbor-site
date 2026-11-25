<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChangeRequest;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChangeRequestController extends AdminResourceController
{
    protected string $model = ChangeRequest::class;

    protected string $route = 'change-requests';

    protected string $permission = 'change_requests';

    protected string $singular = 'Change Request';

    protected string $plural = 'Change Requests';

    protected string $description = 'Scope changes with their cost and timeline impact.';

    protected string $icon = 'fa-code-pull-request';

    protected array $searchable = ['request_number', 'title', 'description'];

    protected array $with = ['project'];

    protected function columns(): array
    {
        return [
            Column::code('request_number', 'Request #'),
            Column::text('title', 'Title'),
            Column::relation('project.name', 'Project'),
            Column::text('requested_by', 'Requested By'),
            Column::money('cost_impact', 'Cost Impact'),
            Column::custom('timeline', 'Timeline', fn (ChangeRequest $request) => $request->timeline_impact_days
                ? e(($request->timeline_impact_days > 0 ? '+' : '').$request->timeline_impact_days.' days')
                : '<span class="admin-cell-muted">—</span>'),
            Column::badge('status', 'Status', ChangeRequest::STATUSES),
            Column::boolean('client_approved', 'Client Approved'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('project_id', 'Project', $this->options->projects(), ['required' => true]),
            Field::text('requested_by', 'Requested By', ['placeholder' => 'Client contact name']),
            Field::money('cost_impact', 'Cost Impact'),
            Field::number('timeline_impact_days', 'Timeline Impact (days)', ['help' => 'Use a negative number if the change saves time.']),
            Field::select('status', 'Status', ChangeRequest::STATUSES, ['required' => true]),
            Field::date('client_approved_at', 'Client Approved On'),
            Field::checkbox('client_approved', 'Approved by client'),
            Field::textarea('description', 'Description', ['rows' => 5]),
            Field::textarea('reason', 'Reason for Change'),
            Field::textarea('remarks', 'Internal Remarks'),
            Field::file('attachment', 'Attachment', ['attachment' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'status', 'label' => 'Status', 'options' => ChangeRequest::STATUSES],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'exists:projects,id'],
            'requested_by' => ['nullable', 'string', 'max:255'],
            'cost_impact' => ['nullable', 'numeric'],
            'timeline_impact_days' => ['nullable', 'integer', 'min:-365', 'max:365'],
            'status' => ['required', Rule::in(array_keys(ChangeRequest::STATUSES))],
            'client_approved' => ['boolean'],
            'client_approved_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'change-requests');
        }
    }
}
