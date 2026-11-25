<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Models\Task;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends AdminResourceController
{
    protected string $model = Project::class;

    protected string $route = 'projects';

    protected string $permission = 'projects';

    protected string $singular = 'Project';

    protected string $plural = 'Projects';

    protected string $description = 'Client delivery projects, their team, budget and progress.';

    protected string $icon = 'fa-diagram-project';

    protected array $searchable = ['project_number', 'name', 'type'];

    protected array $with = ['client', 'manager'];

    protected array $withCount = ['tasks', 'bugs'];

    protected array $relationFields = ['members'];

    protected bool $hasShow = true;

    protected function columns(): array
    {
        return [
            Column::code('project_number', 'Project #'),
            Column::text('name', 'Project'),
            Column::relation('client.company_name', 'Client'),
            Column::relation('manager.name', 'Manager'),
            Column::money('budget', 'Budget'),
            Column::custom('deadline', 'Deadline', function (Project $project) {
                if (! $project->deadline) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                $label = e($project->deadline->format('M d, Y'));

                return $project->isOverdue()
                    ? '<span class="admin-badge admin-badge--overdue">'.$label.'</span>'
                    : $label;
            }),
            Column::progress('progress', 'Progress'),
            Column::badge('status', 'Status', Project::STATUSES),
            Column::badge('priority', 'Priority', Project::PRIORITIES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Project'),
            Field::text('name', 'Project Name', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients(), ['required' => true]),
            Field::text('type', 'Project Type', ['placeholder' => 'Web Application, Mobile App...']),
            Field::select('quotation_id', 'Source Quotation', $this->options->quotations()),
            Field::select('project_manager_id', 'Project Manager', $this->options->employees()),
            Field::multiselect('members', 'Team Members', $this->options->employees()),
            Field::list('technologies', 'Technologies', ['placeholder' => 'Laravel, Vue, MySQL']),

            Field::heading('Schedule & Budget'),
            Field::date('start_date', 'Start Date'),
            Field::date('deadline', 'Deadline'),
            Field::date('completed_at', 'Completed On'),
            Field::money('budget', 'Budget'),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::number('progress', 'Progress (%)', ['attributes' => ['min' => 0, 'max' => 100]]),
            Field::select('status', 'Status', Project::STATUSES, ['required' => true]),
            Field::select('priority', 'Priority', Project::PRIORITIES, ['required' => true]),

            Field::heading('Links'),
            Field::url('repository_url', 'Repository URL'),
            Field::url('staging_url', 'Staging URL'),
            Field::url('live_url', 'Live URL'),

            Field::heading('Detail'),
            Field::textarea('description', 'Description', ['rows' => 5]),
            Field::textarea('notes', 'Internal Notes'),
            Field::file('attachment', 'Attachment', ['attachment' => true, 'help' => 'Briefs, contracts or design files.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Project::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => Project::PRIORITIES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'project_manager_id', 'label' => 'Manager', 'options' => $this->options->employees()],
            ['key' => 'deadline_to', 'label' => 'Deadline before', 'input' => 'date', 'type' => 'date_to', 'column' => 'deadline'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'exists:clients,id'],
            'type' => ['nullable', 'string', 'max:150'],
            'quotation_id' => ['nullable', 'exists:quotations,id'],
            'project_manager_id' => ['nullable', 'exists:employees,id'],
            'members' => ['array'],
            'members.*' => ['exists:employees,id'],
            'technologies' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'completed_at' => ['nullable', 'date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['required', Rule::in(array_keys(Project::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(Project::PRIORITIES))],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'staging_url' => ['nullable', 'url', 'max:255'],
            'live_url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        /** @var Project $record */
        $record->members()->sync($request->input('members', []));

        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'projects');
        }
    }

    /** Project workspace: overview plus every related record. */
    public function show(Project $project): View
    {
        $this->authorizeAction('view');

        $project->load([
            'client', 'manager', 'members', 'quotation', 'lead', 'attachments',
            'milestones', 'requirements', 'changeRequests',
            'bugs.assignee', 'invoices', 'expenses', 'tickets',
            'tasks' => fn ($query) => $query->with('assignees')->orderByRaw("FIELD(status, 'todo','in_progress','review','on_hold','completed')")->orderBy('due_date'),
            'timesheets' => fn ($query) => $query->with('employee')->latest('date')->limit(20),
        ]);

        $financials = [
            'budget' => (float) $project->budget,
            'invoiced' => (float) $project->invoices->whereNotIn('status', ['draft', 'cancelled'])->sum('total'),
            'received' => (float) $project->invoices->sum('paid_amount'),
            'expenses' => (float) $project->expenses->where('status', 'approved')->sum('amount'),
            'logged_hours' => (float) $project->timesheets->sum('hours'),
        ];

        $financials['profit'] = round($financials['received'] - $financials['expenses'], 2);

        return view('admin.projects.show', [
            'project' => $project,
            'financials' => $financials,
            'taskSummary' => [
                'total' => $project->tasks->count(),
                'completed' => $project->tasks->where('status', Task::STATUS_COMPLETED)->count(),
                'overdue' => $project->tasks->filter(fn (Task $task) => $task->isOverdue())->count(),
            ],
        ]);
    }

    /** Recomputes progress from completed tasks. */
    public function syncProgress(Project $project): RedirectResponse
    {
        $this->authorizeAction('edit');

        $project->update(['progress' => $project->taskProgress()]);

        return back()->with('success', 'Project progress recalculated from its tasks.');
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return $request->boolean('overdue')
            ? $query->active()->whereDate('deadline', '<', today())
            : $query;
    }
}
