@extends('admin.layouts.admin')

@section('title', $employee->name)
@section('page-title', 'Employee')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $employee->name }}
                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $employee->status) }}">
                    {{ \App\Models\Employee::STATUSES[$employee->status] ?? $employee->status }}
                </span>
            </h1>
            <p>{{ $employee->employee_code }} &middot; {{ $employee->designation ?: 'No designation' }}</p>
        </div>
        <div class="admin-btn-group">
            @can('employees.edit')
                <a href="{{ route('admin.employees.edit', $employee) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.employees.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Open Tasks</div><div class="admin-kpi__value">{{ $stats['open_tasks'] }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--primary"><i class="fas fa-list-check"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Overdue Tasks</div><div class="admin-kpi__value">{{ $stats['overdue_tasks'] }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--danger"><i class="fas fa-triangle-exclamation"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Active Projects</div><div class="admin-kpi__value">{{ $stats['active_projects'] }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-diagram-project"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Hours This Month</div>
                <div class="admin-kpi__value">{{ number_format($stats['hours_this_month'], 1) }}</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--purple"><i class="fas fa-clock"></i></div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Open Tasks</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Task</th><th>Project</th><th>Due</th><th>Priority</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($employee->tasks as $task)
                                <tr>
                                    <td><a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a></td>
                                    <td>{{ $task->project?->name ?? '—' }}</td>
                                    <td>
                                        @if ($task->isOverdue())
                                            <span class="admin-badge admin-badge--overdue">{{ $task->due_date->format('M d') }}</span>
                                        @else
                                            {{ $task->due_date?->format('M d, Y') ?? '—' }}
                                        @endif
                                    </td>
                                    <td><span class="admin-badge admin-badge--{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }}</span></td>
                                    <td><span class="admin-badge admin-badge--{{ str_replace('_', '-', $task->status) }}">{{ \App\Models\Task::STATUSES[$task->status] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-state">No open tasks.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Projects</h2></div>
                <div class="admin-mini-list">
                    @forelse ($employee->projects as $project)
                        <a href="{{ route('admin.projects.show', $project) }}" class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $project->name }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ $project->client?->displayName() }}
                                    @if ($project->pivot->role) &middot; {{ $project->pivot->role }}@endif
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ str_replace('_', '-', $project->status) }}">{{ \App\Models\Project::STATUSES[$project->status] }}</span>
                        </a>
                    @empty
                        <div class="admin-mini-list__empty">Not assigned to any project.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Recent Timesheets</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Date</th><th>Hours</th><th>Billable</th><th>Status</th><th>Description</th></tr></thead>
                        <tbody>
                            @forelse ($employee->timesheets as $timesheet)
                                <tr>
                                    <td>{{ $timesheet->date?->format('M d, Y') }}</td>
                                    <td>{{ $timesheet->hours }}</td>
                                    <td>{{ $timesheet->is_billable ? 'Yes' : 'No' }}</td>
                                    <td><span class="admin-badge admin-badge--{{ $timesheet->status }}">{{ \App\Models\Timesheet::STATUSES[$timesheet->status] }}</span></td>
                                    <td class="admin-cell-muted">{{ Str::limit($timesheet->description, 50) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-state">No time logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Leave History</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($employee->leaves as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType?->name ?? '—' }}</td>
                                    <td>{{ $leave->from_date?->format('M d, Y') }}</td>
                                    <td>{{ $leave->to_date?->format('M d, Y') }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td><span class="admin-badge admin-badge--{{ $leave->status }}">{{ \App\Models\Leave::STATUSES[$leave->status] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-state">No leave taken.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Profile</h2></div>
                <div class="admin-card__body">
                    @if ($employee->photo)
                        <img src="{{ uploaded_url($employee->photo) }}" alt="" style="width:110px; height:110px; object-fit:cover; border-radius:12px; margin-bottom:14px;">
                    @endif
                    <dl class="admin-definition-list">
                        <div><dt>Department</dt><dd>{{ $employee->department?->name ?? '—' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $employee->email ?: '—' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $employee->phone ?: '—' }}</dd></div>
                        <div><dt>WhatsApp</dt><dd>{{ $employee->whatsapp ?: '—' }}</dd></div>
                        <div><dt>Employment</dt><dd>{{ \App\Models\Employee::EMPLOYMENT_TYPES[$employee->employment_type] ?? '—' }}</dd></div>
                        <div><dt>Joined</dt><dd>{{ $employee->joining_date?->format('M d, Y') ?? '—' }}</dd></div>
                        @if ($employee->exit_date)
                            <div><dt>Left</dt><dd>{{ $employee->exit_date->format('M d, Y') }}</dd></div>
                        @endif
                        <div><dt>Login Account</dt><dd>{{ $employee->admin?->email ?? 'No login' }}</dd></div>
                        @if ($canSeeSalary)
                            <div><dt>Salary</dt><dd>{{ $employee->salary ? money($employee->salary) : '—' }}</dd></div>
                            <div><dt>Hourly Rate</dt><dd>{{ $employee->hourly_rate ? money($employee->hourly_rate) : '—' }}</dd></div>
                        @endif
                    </dl>

                    @if (is_array($employee->skills) && $employee->skills)
                        <div style="margin-top:14px">
                            @foreach ($employee->skills as $skill)
                                <span class="admin-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if ($employee->managedProjects->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Managing</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($employee->managedProjects as $project)
                            <a href="{{ route('admin.projects.show', $project) }}" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $project->name }}</div>
                                    <div class="admin-mini-list__meta">{{ $project->client?->displayName() }}</div></div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($employee->notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Notes</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $employee->notes }}</div></div>
                </div>
            @endif
        </div>
    </div>

@endsection
