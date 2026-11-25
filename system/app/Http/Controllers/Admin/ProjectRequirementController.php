<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProjectRequirement;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectRequirementController extends AdminResourceController
{
    protected string $model = ProjectRequirement::class;

    protected string $route = 'requirements';

    protected string $permission = 'requirements';

    protected string $singular = 'Requirement';

    protected string $plural = 'Requirements / SRS';

    protected string $description = 'Functional and non-functional requirements, with client sign-off.';

    protected string $icon = 'fa-clipboard-list';

    protected array $searchable = ['code', 'title', 'description'];

    protected array $with = ['project'];

    protected function columns(): array
    {
        return [
            Column::code('code', 'Ref'),
            Column::text('title', 'Requirement'),
            Column::relation('project.name', 'Project'),
            Column::badge('requirement_type', 'Type', ProjectRequirement::TYPES),
            Column::badge('priority', 'Priority', ProjectRequirement::PRIORITIES),
            Column::badge('status', 'Status', ProjectRequirement::STATUSES),
            Column::boolean('client_approved', 'Client Approved'),
            Column::text('version', 'Version'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Requirement Title', ['required' => true]),
            Field::select('project_id', 'Project', $this->options->projects(), ['required' => true]),
            Field::text('code', 'Reference Code', ['placeholder' => 'FR-01']),
            Field::select('requirement_type', 'Type', ProjectRequirement::TYPES, ['required' => true]),
            Field::select('priority', 'Priority', ProjectRequirement::PRIORITIES, ['required' => true]),
            Field::select('status', 'Status', ProjectRequirement::STATUSES, ['required' => true]),
            Field::text('version', 'Version', ['placeholder' => '1.0']),
            Field::date('client_approved_at', 'Client Approved On'),
            Field::checkbox('client_approved', 'Approved by client'),
            Field::textarea('description', 'Description', ['rows' => 6]),
            Field::textarea('change_log', 'Change Log', ['help' => 'What changed in this version and why.']),
            Field::file('attachment', 'Attachment', ['attachment' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'requirement_type', 'label' => 'Type', 'options' => ProjectRequirement::TYPES],
            ['key' => 'status', 'label' => 'Status', 'options' => ProjectRequirement::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => ProjectRequirement::PRIORITIES],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'exists:projects,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'requirement_type' => ['required', Rule::in(array_keys(ProjectRequirement::TYPES))],
            'priority' => ['required', Rule::in(array_keys(ProjectRequirement::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(ProjectRequirement::STATUSES))],
            'version' => ['nullable', 'string', 'max:20'],
            'client_approved' => ['boolean'],
            'client_approved_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'change_log' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'requirements');
        }
    }
}
