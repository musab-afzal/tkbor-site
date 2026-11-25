<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProjectMilestone;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ProjectMilestoneController extends AdminResourceController
{
    protected string $model = ProjectMilestone::class;

    protected string $route = 'milestones';

    protected string $permission = 'milestones';

    protected string $singular = 'Milestone';

    protected string $plural = 'Milestones';

    protected string $description = 'Delivery and payment milestones across all projects.';

    protected string $icon = 'fa-flag-checkered';

    protected array $searchable = ['title', 'description'];

    protected array $with = ['project.client'];

    protected string $defaultSort = 'due_date';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('title', 'Milestone'),
            Column::relation('project.name', 'Project'),
            Column::custom('due', 'Due Date', function (ProjectMilestone $milestone) {
                if (! $milestone->due_date) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                $label = e($milestone->due_date->format('M d, Y'));

                return $milestone->isOverdue()
                    ? '<span class="admin-badge admin-badge--overdue">'.$label.'</span>'
                    : $label;
            }),
            Column::money('amount', 'Amount'),
            Column::boolean('is_payment_milestone', 'Payment'),
            Column::progress('progress', 'Progress'),
            Column::badge('status', 'Status', ProjectMilestone::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('project_id', 'Project', $this->options->projects(), ['required' => true]),
            Field::date('start_date', 'Start Date'),
            Field::date('due_date', 'Due Date'),
            Field::money('amount', 'Amount'),
            Field::number('progress', 'Progress (%)', ['attributes' => ['min' => 0, 'max' => 100]]),
            Field::select('status', 'Status', ProjectMilestone::STATUSES, ['required' => true]),
            Field::number('sort_order', 'Sort Order'),
            Field::checkbox('is_payment_milestone', 'Payment milestone', ['help' => 'An invoice can be raised against it.']),
            Field::textarea('description', 'Description'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'status', 'label' => 'Status', 'options' => ProjectMilestone::STATUSES],
            ['key' => 'is_payment_milestone', 'label' => 'Type', 'type' => 'boolean', 'options' => ['1' => 'Payment', '0' => 'Delivery']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'exists:projects,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['required', Rule::in(array_keys(ProjectMilestone::STATUSES))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_payment_milestone' => ['boolean'],
            'description' => ['nullable', 'string'],
        ];
    }
}
