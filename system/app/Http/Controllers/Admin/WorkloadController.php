<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use App\Support\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Workload is derived from live task and project assignments — there is no
 * separate workload table to keep in sync.
 */
class WorkloadController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('workload.view');

        $employees = Employee::query()
            ->with('department')
            ->when($request->filled('department_id'), fn (Builder $q) => $q->where('department_id', $request->input('department_id')))
            ->when(! $request->boolean('include_inactive'), fn (Builder $q) => $q->active())
            ->withCount([
                'tasks as open_tasks_count' => fn (Builder $q) => $q->where('tasks.status', '!=', Task::STATUS_COMPLETED),
                'tasks as in_progress_count' => fn (Builder $q) => $q->where('tasks.status', Task::STATUS_IN_PROGRESS),
                'tasks as overdue_tasks_count' => fn (Builder $q) => $q->where('tasks.status', '!=', Task::STATUS_COMPLETED)
                    ->whereDate('tasks.due_date', '<', today()),
                'tasks as due_today_count' => fn (Builder $q) => $q->where('tasks.status', '!=', Task::STATUS_COMPLETED)
                    ->whereDate('tasks.due_date', today()),
                'tasks as completed_count' => fn (Builder $q) => $q->where('tasks.status', Task::STATUS_COMPLETED),
                'projects as active_projects_count' => fn (Builder $q) => $q->whereIn('projects.status', ['planning', 'active', 'on_hold', 'testing']),
            ])
            ->withSum([
                'tasks as estimated_hours_sum' => fn (Builder $q) => $q->where('tasks.status', '!=', Task::STATUS_COMPLETED),
            ], 'estimated_hours')
            ->withSum([
                'timesheets as hours_this_month' => fn (Builder $q) => $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]),
            ], 'hours')
            ->orderByDesc('open_tasks_count')
            ->get();

        return view('admin.workload.index', [
            'employees' => $employees,
            'departments' => app(Options::class)->departments(),
        ]);
    }
}
