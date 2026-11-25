<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bug;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BugController extends AdminResourceController
{
    protected string $model = Bug::class;

    protected string $route = 'bugs';

    protected string $permission = 'bugs';

    protected string $singular = 'Bug';

    protected string $plural = 'Bugs & Issues';

    protected string $description = 'Defects raised against projects, with severity and resolution.';

    protected string $icon = 'fa-bug';

    protected array $searchable = ['bug_number', 'title', 'description'];

    protected array $with = ['project', 'assignee'];

    protected function columns(): array
    {
        return [
            Column::code('bug_number', 'Bug #'),
            Column::text('title', 'Title'),
            Column::relation('project.name', 'Project'),
            Column::badge('severity', 'Severity', Bug::SEVERITIES),
            Column::badge('priority', 'Priority', Bug::PRIORITIES),
            Column::relation('assignee.name', 'Assigned To'),
            Column::badge('status', 'Status', Bug::STATUSES),
            Column::date('reported_date', 'Reported'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('project_id', 'Project', $this->options->projects(), ['required' => true]),
            Field::select('severity', 'Severity', Bug::SEVERITIES, ['required' => true]),
            Field::select('priority', 'Priority', Bug::PRIORITIES, ['required' => true]),
            Field::select('assigned_to', 'Assigned Developer', $this->options->employees()),
            Field::select('status', 'Status', Bug::STATUSES, ['required' => true]),
            Field::date('reported_date', 'Reported Date'),
            Field::date('resolved_date', 'Resolved Date'),
            Field::textarea('description', 'Description', ['rows' => 4]),
            Field::textarea('steps_to_reproduce', 'Steps to Reproduce', ['rows' => 4]),
            Field::textarea('resolution', 'Resolution'),
            Field::file('screenshot', 'Screenshot / Attachment', ['attachment' => true, 'accept' => 'image/*,.pdf']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'status', 'label' => 'Status', 'options' => Bug::STATUSES],
            ['key' => 'severity', 'label' => 'Severity', 'options' => Bug::SEVERITIES],
            ['key' => 'assigned_to', 'label' => 'Assignee', 'options' => $this->options->employees()],
            ['key' => 'open', 'label' => 'View', 'options' => ['1' => 'Open only']],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return $request->boolean('open') ? $query->open() : $query;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'exists:projects,id'],
            'severity' => ['required', Rule::in(array_keys(Bug::SEVERITIES))],
            'priority' => ['required', Rule::in(array_keys(Bug::PRIORITIES))],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'status' => ['required', Rule::in(array_keys(Bug::STATUSES))],
            'reported_date' => ['nullable', 'date'],
            'resolved_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'steps_to_reproduce' => ['nullable', 'string'],
            'resolution' => ['nullable', 'string'],
            'screenshot' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:8192'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('screenshot')) {
            $this->uploader->attachTo($record, $request->file('screenshot'), 'bugs');
        }
    }
}
