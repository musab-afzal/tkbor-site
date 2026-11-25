<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use App\Notifications\TaskAssigned;
use App\Services\NotificationDispatcher;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Tasks power the list view, the kanban board and every "due today / upcoming /
 * overdue" screen. Those screens are filtered views of this one table — there
 * is no duplicated task data anywhere.
 */
class TaskController extends AdminResourceController
{
    protected string $model = Task::class;

    protected string $route = 'tasks';

    protected string $permission = 'tasks';

    protected string $singular = 'Task';

    protected string $plural = 'Tasks';

    protected string $description = 'Work assigned across projects, clients, departments and the company.';

    protected string $icon = 'fa-list-check';

    protected array $searchable = ['task_number', 'title', 'description'];

    protected array $with = ['project', 'client', 'assignees', 'department'];

    protected array $relationFields = ['assignees', 'tags_input', 'dependencies'];

    protected bool $hasShow = true;

    protected string $defaultSort = 'due_date';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::code('task_number', 'Task #'),
            Column::text('title', 'Title'),
            Column::custom('context', 'For', fn (Task $task) => e(
                $task->project?->name ?? $task->client?->displayName() ?? $task->department?->name ?? 'Company'
            )),
            Column::custom('assignees', 'Assigned To', fn (Task $task) => $task->assignees->isEmpty()
                ? '<span class="admin-cell-muted">Unassigned</span>'
                : $task->assignees->map(fn ($employee) => '<span class="admin-chip">'.e($employee->name).'</span>')->implode('')),
            Column::custom('due', 'Due', function (Task $task) {
                if (! $task->due_date) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                $label = e($task->due_date->format('M d, Y'));

                if ($task->isOverdue()) {
                    return '<span class="admin-badge admin-badge--overdue">'.$label.'</span>';
                }

                return $task->isDueToday()
                    ? '<span class="admin-badge admin-badge--warning">Today</span>'
                    : $label;
            }),
            Column::badge('priority', 'Priority', Task::PRIORITIES),
            Column::badge('status', 'Status', Task::STATUSES),
            Column::progress('progress', 'Progress'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Task'),
            Field::text('title', 'Title', ['required' => true]),
            Field::select('status', 'Status', Task::STATUSES, ['required' => true]),
            Field::select('priority', 'Priority', Task::PRIORITIES, ['required' => true]),
            Field::multiselect('assignees', 'Assigned To', $this->options->employees()),
            Field::textarea('description', 'Description', ['rows' => 4]),

            Field::heading('Belongs To'),
            Field::select('project_id', 'Project', $this->options->projects(), [
                'help' => 'Leave every option blank for a general company task.',
            ]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('department_id', 'Department', $this->options->departments()),

            Field::heading('Schedule'),
            Field::date('start_date', 'Start Date'),
            Field::date('due_date', 'Due Date'),
            Field::time('due_time', 'Due Time'),
            Field::number('estimated_hours', 'Estimated Hours', ['attributes' => ['step' => '0.25', 'min' => '0']]),
            Field::number('actual_hours', 'Actual Hours', ['attributes' => ['step' => '0.25', 'min' => '0']]),
            Field::number('progress', 'Progress (%)', ['attributes' => ['min' => 0, 'max' => 100]]),

            Field::heading('Extras'),
            Field::text('tags_input', 'Tags', [
                'full' => true,
                'placeholder' => 'design, urgent, phase-1',
                'help' => 'Comma separated. New tags are created automatically.',
                'value' => $this->currentRecord?->exists
                    ? $this->currentRecord->tags->pluck('name')->implode(', ')
                    : '',
            ]),
            Field::multiselect('dependencies', 'Depends On', $this->dependencyOptions()),
            Field::file('attachment', 'Attachment', ['attachment' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Task::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => Task::PRIORITIES],
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'department_id', 'label' => 'Department', 'options' => $this->options->departments()],
            ['key' => 'employee_id', 'label' => 'Assignee', 'options' => $this->options->employees()],
            ['key' => 'from', 'label' => 'Due from', 'input' => 'date', 'type' => 'date_from', 'column' => 'due_date'],
            ['key' => 'to', 'label' => 'Due to', 'input' => 'date', 'type' => 'date_to', 'column' => 'due_date'],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        if ($request->filled('employee_id')) {
            $query->whereHas('assignees', fn (Builder $q) => $q->where('employees.id', $request->input('employee_id')));
        }

        return match ($request->string('view')->toString()) {
            'due_today' => $query->dueToday(),
            'upcoming' => $query->upcoming(14),
            'overdue' => $query->overdue(),
            'mine' => $query->assignedToAdmin(Auth::guard('admin')->user()),
            'assigned' => $query->where('assigned_by', Auth::guard('admin')->id()),
            default => $query,
        };
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'assignees' => ['array'],
            'assignees.*' => ['exists:employees,id'],
            'dependencies' => ['array'],
            'dependencies.*' => ['exists:tasks,id'],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'actual_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tags_input' => ['nullable', 'string', 'max:500'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        if (! $record->exists) {
            $data['assigned_by'] = Auth::guard('admin')->id();
        }

        return $data;
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        /** @var Task $record */
        $previousAssignees = $record->assignees()->pluck('employees.id')->all();

        $record->assignees()->sync($request->input('assignees', []));

        // Only tell people who were not already on the task.
        $newAssignees = array_diff($request->input('assignees', []), $previousAssignees);

        if ($newAssignees !== []) {
            app(NotificationDispatcher::class)->toEmployees($newAssignees, new TaskAssigned($record));
        }

        // A task can never depend on itself.
        $record->dependencies()->sync(
            collect($request->input('dependencies', []))->reject(fn ($id) => (int) $id === $record->id)->all()
        );

        $record->syncTagsFromString($request->input('tags_input'));

        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'tasks');
        }
    }

    // ------------------------------------------------------------ task views

    public function board(Request $request): View
    {
        $this->authorizeAction('view');

        $tasks = Task::query()
            ->with(['project', 'assignees'])
            ->when($request->filled('project_id'), fn (Builder $q) => $q->where('project_id', $request->input('project_id')))
            ->when($request->filled('employee_id'), fn (Builder $q) => $q->whereHas('assignees', fn (Builder $e) => $e->where('employees.id', $request->input('employee_id'))))
            ->orderBy('sort_order')
            ->orderBy('due_date')
            ->get()
            ->groupBy('status');

        return view('admin.tasks.board', [
            'grouped' => $tasks,
            'projects' => $this->options->projects(),
            'employees' => $this->options->employees(),
        ]);
    }

    public function dueToday(Request $request): View
    {
        return $this->filteredView($request, 'due_today', 'Tasks Due Today', 'Everything with a due date of '.today()->format('M d, Y').'.');
    }

    public function upcoming(Request $request): View
    {
        return $this->filteredView($request, 'upcoming', 'Upcoming Tasks', 'Due within the next 14 days.');
    }

    public function overdue(Request $request): View
    {
        return $this->filteredView($request, 'overdue', 'Overdue Tasks', 'Past their due date and not yet completed.');
    }

    public function mine(Request $request): View
    {
        return $this->filteredView($request, 'mine', 'My Tasks', 'Tasks assigned to you.');
    }

    public function assigned(Request $request): View
    {
        return $this->filteredView($request, 'assigned', 'Assigned Tasks', 'Tasks you assigned to other people.');
    }

    public function show(Task $task): View
    {
        $this->authorizeAction('view');

        $task->load([
            'project', 'client', 'department', 'assignees', 'assignedBy',
            'checklistItems', 'dependencies', 'tags', 'attachments',
            'comments.admin', 'timesheets.employee',
        ]);

        return view('admin.tasks.show', compact('task'));
    }

    public function complete(Task $task): RedirectResponse
    {
        $this->authorizeAction('edit');

        $task->update(['status' => Task::STATUS_COMPLETED, 'progress' => 100]);

        return back()->with('success', 'Task marked as completed.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeAction('edit');

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
        ]);

        $task->update($data);

        return back()->with('success', 'Task status updated.');
    }

    public function addComment(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeAction('view');

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $task->comments()->create($data + ['admin_id' => Auth::guard('admin')->id()]);

        return back()->with('success', 'Comment added.');
    }

    public function addChecklistItem(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeAction('edit');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $task->checklistItems()->create($data + [
            'sort_order' => (int) $task->checklistItems()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Checklist item added.');
    }

    public function toggleChecklistItem(Task $task, int $item): RedirectResponse
    {
        $this->authorizeAction('edit');

        $checklistItem = $task->checklistItems()->findOrFail($item);
        $checklistItem->update(['is_done' => ! $checklistItem->is_done]);

        return back();
    }

    public function deleteChecklistItem(Task $task, int $item): RedirectResponse
    {
        $this->authorizeAction('edit');

        $task->checklistItems()->findOrFail($item)->delete();

        return back()->with('success', 'Checklist item removed.');
    }

    protected function filteredView(Request $request, string $view, string $heading, string $description): View
    {
        $this->authorizeAction('view');

        $request->merge(['view' => $view]);

        $records = $this->applyFilters($this->baseQuery(), $request)
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->paginate($this->perPage)
            ->withQueryString();

        return view($this->indexView, array_merge($this->sharedViewData(), [
            'records' => $records,
            'columns' => $this->columns(),
            'filterControls' => $this->filters(),
            'plural' => $heading,
            'moduleDescription' => $description,
        ]));
    }

    /** @return array<int, string> */
    protected function dependencyOptions(): array
    {
        return Task::query()
            ->when($this->currentRecord?->exists, fn (Builder $q) => $q->whereKeyNot($this->currentRecord->getKey()))
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->mapWithKeys(fn (Task $task) => [$task->id => $task->task_number.' - '.$task->title])
            ->all();
    }
}
