@extends('admin.layouts.admin')

@section('title', 'Workload')
@section('page-title', 'Employee Workload')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Employee Workload</h1>
            <p>Calculated live from task and project assignments.</p>
        </div>
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.workload.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field">
                <label class="admin-label" for="department_id">Department</label>
                <select id="department_id" name="department_id" class="admin-select">
                    <option value="">All departments</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('department_id') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="include_inactive">Include</label>
                <select id="include_inactive" name="include_inactive" class="admin-select">
                    <option value="">Active employees only</option>
                    <option value="1" @selected(request('include_inactive'))>All employees</option>
                </select>
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.workload.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Employee</th><th>Department</th><th>Open Tasks</th><th>In Progress</th>
                        <th>Due Today</th><th>Overdue</th><th>Completed</th><th>Projects</th>
                        <th>Est. Hours</th><th>Hours This Month</th><th>Load</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        @php
                            // A rough load indicator: 10 open tasks reads as fully loaded.
                            $load = min(100, (int) round($employee->open_tasks_count / 10 * 100));
                        @endphp
                        <tr>
                            <td>
                                <div class="admin-cell-stack">
                                    <a href="{{ route('admin.employees.show', $employee) }}">{{ $employee->name }}</a>
                                    <small>{{ $employee->designation ?: '—' }}</small>
                                </div>
                            </td>
                            <td>{{ $employee->department?->name ?? '—' }}</td>
                            <td><strong>{{ $employee->open_tasks_count }}</strong></td>
                            <td>{{ $employee->in_progress_count }}</td>
                            <td>
                                @if ($employee->due_today_count > 0)
                                    <span class="admin-badge admin-badge--warning">{{ $employee->due_today_count }}</span>
                                @else — @endif
                            </td>
                            <td>
                                @if ($employee->overdue_tasks_count > 0)
                                    <span class="admin-badge admin-badge--overdue">{{ $employee->overdue_tasks_count }}</span>
                                @else — @endif
                            </td>
                            <td>{{ $employee->completed_count }}</td>
                            <td>{{ $employee->active_projects_count }}</td>
                            <td>{{ $employee->estimated_hours_sum ? number_format((float) $employee->estimated_hours_sum, 1) : '—' }}</td>
                            <td>{{ $employee->hours_this_month ? number_format((float) $employee->hours_this_month, 1) : '—' }}</td>
                            <td>
                                <div class="admin-progress" title="{{ $load }}% loaded">
                                    <div class="admin-progress__bar" style="width: {{ $load }}%"></div>
                                    <span class="admin-progress__label">{{ $load }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="admin-empty-state"><i class="fas fa-users"></i>No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
