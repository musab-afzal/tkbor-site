<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use App\Models\Timesheet;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TimesheetController extends AdminResourceController
{
    protected string $model = Timesheet::class;

    protected string $route = 'timesheets';

    protected string $permission = 'timesheets';

    protected string $singular = 'Timesheet Entry';

    protected string $plural = 'Timesheets';

    protected string $description = 'Time logged against projects and tasks, billable or not.';

    protected string $icon = 'fa-clock';

    protected array $searchable = ['description'];

    protected array $with = ['employee', 'project', 'task'];

    protected string $defaultSort = 'date';

    protected function columns(): array
    {
        return [
            Column::date('date', 'Date'),
            Column::relation('employee.name', 'Employee'),
            Column::relation('project.name', 'Project'),
            Column::relation('task.title', 'Task'),
            Column::number('hours', 'Hours', ['decimals' => 2]),
            Column::boolean('is_billable', 'Billable'),
            Column::badge('status', 'Status', Timesheet::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::select('employee_id', 'Employee', $this->options->employees(), ['required' => true]),
            Field::select('project_id', 'Project', $this->options->projects()),
            Field::select('task_id', 'Task', $this->taskOptions()),
            Field::date('date', 'Date', ['required' => true]),
            Field::time('start_time', 'Start Time'),
            Field::time('end_time', 'End Time', ['help' => 'Hours are calculated automatically when both times are set.']),
            Field::number('hours', 'Hours', ['attributes' => ['step' => '0.25', 'min' => '0']]),
            Field::money('hourly_rate', 'Hourly Rate', ['help' => 'Leave blank to use the employee rate.']),
            Field::select('status', 'Status', Timesheet::STATUSES, ['required' => true]),
            Field::checkbox('is_billable', 'Billable to the client'),
            Field::textarea('description', 'What was done'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'employee_id', 'label' => 'Employee', 'options' => $this->options->employees()],
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'status', 'label' => 'Status', 'options' => Timesheet::STATUSES],
            ['key' => 'is_billable', 'label' => 'Billable', 'type' => 'boolean', 'options' => ['1' => 'Billable', '0' => 'Non-billable']],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'date'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'date'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(array_keys(Timesheet::STATUSES))],
            'is_billable' => ['boolean'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function approve(Timesheet $timesheet): RedirectResponse
    {
        $this->authorize('timesheets.approve');

        $timesheet->update([
            'status' => Timesheet::STATUS_APPROVED,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Timesheet approved.');
    }

    public function reject(Timesheet $timesheet): RedirectResponse
    {
        $this->authorize('timesheets.approve');

        $timesheet->update([
            'status' => 'rejected',
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Timesheet rejected.');
    }

    /** @return array<int, string> */
    protected function taskOptions(): array
    {
        return Task::query()
            ->open()
            ->orderByDesc('id')
            ->limit(300)
            ->get()
            ->mapWithKeys(fn (Task $task) => [$task->id => $task->task_number.' - '.$task->title])
            ->all();
    }
}
