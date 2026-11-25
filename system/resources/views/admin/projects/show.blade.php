@extends('admin.layouts.admin')

@section('title', $project->name)
@section('page-title', 'Project '.$project->project_number)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $project->name }}
                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $project->status) }}">
                    {{ \App\Models\Project::STATUSES[$project->status] ?? $project->status }}
                </span>
                @if ($project->isOverdue())<span class="admin-badge admin-badge--overdue">Overdue</span>@endif
            </h1>
            <p>
                {{ $project->project_number }} &middot;
                <a href="{{ route('admin.clients.show', $project->client) }}">{{ $project->client?->displayName() }}</a>
            </p>
        </div>
        <div class="admin-btn-group">
            @can('tasks.create')
                <a href="{{ route('admin.tasks.create') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-plus"></i> Add Task</a>
            @endcan
            @can('invoices.create')
                <a href="{{ route('admin.invoices.create', ['client_id' => $project->client_id, 'project_id' => $project->id]) }}" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-file-invoice-dollar"></i> New Invoice
                </a>
            @endcan
            @can('projects.edit')
                <a href="{{ route('admin.projects.edit', $project) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Progress</div>
                <div class="admin-kpi__value">{{ $project->progress }}%</div>
                <div class="admin-kpi__meta">{{ $taskSummary['completed'] }}/{{ $taskSummary['total'] }} tasks done</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--primary"><i class="fas fa-chart-simple"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Budget</div><div class="admin-kpi__value">{{ money($financials['budget'], $project->currency) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-sack-dollar"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Received</div>
                <div class="admin-kpi__value">{{ money($financials['received'], $project->currency) }}</div>
                <div class="admin-kpi__meta">of {{ money($financials['invoiced'], $project->currency) }} invoiced</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-money-bill-transfer"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Project Profit</div>
                <div class="admin-kpi__value">{{ money($financials['profit'], $project->currency) }}</div>
                <div class="admin-kpi__meta">{{ money($financials['expenses'], $project->currency) }} costs</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--{{ $financials['profit'] >= 0 ? 'success' : 'danger' }}"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Logged Hours</div>
                <div class="admin-kpi__value">{{ number_format($financials['logged_hours'], 1) }}</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--purple"><i class="fas fa-clock"></i></div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Tasks <span class="admin-tab__count">{{ $taskSummary['total'] }}</span></h2>
                    @can('projects.edit')
                        <form action="{{ route('admin.projects.sync-progress', $project) }}" method="POST" class="admin-inline-form">
                            @csrf @method('PATCH')
                            <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-rotate"></i> Recalculate progress</button>
                        </form>
                    @endcan
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Task</th><th>Assignees</th><th>Due</th><th>Priority</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($project->tasks as $task)
                                <tr>
                                    <td>
                                        <div class="admin-cell-stack">
                                            <a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a>
                                            <small>{{ $task->task_number }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @forelse ($task->assignees as $assignee)
                                            <span class="admin-chip">{{ $assignee->name }}</span>
                                        @empty
                                            <span class="admin-cell-muted">Unassigned</span>
                                        @endforelse
                                    </td>
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
                                <tr><td colspan="5" class="admin-empty-state">No tasks on this project yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Milestones</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Milestone</th><th>Due</th><th>Amount</th><th>Progress</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($project->milestones as $milestone)
                                <tr>
                                    <td>{{ $milestone->title }}</td>
                                    <td>
                                        @if ($milestone->isOverdue())
                                            <span class="admin-badge admin-badge--overdue">{{ $milestone->due_date->format('M d, Y') }}</span>
                                        @else
                                            {{ $milestone->due_date?->format('M d, Y') ?? '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $milestone->amount ? money($milestone->amount, $project->currency) : '—' }}</td>
                                    <td>
                                        <div class="admin-progress"><div class="admin-progress__bar" style="width: {{ $milestone->progress }}%"></div>
                                            <span class="admin-progress__label">{{ $milestone->progress }}%</span></div>
                                    </td>
                                    <td><span class="admin-badge admin-badge--{{ str_replace('_', '-', $milestone->status) }}">{{ \App\Models\ProjectMilestone::STATUSES[$milestone->status] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-state">No milestones defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Requirements / SRS</h2></div>
                <div class="admin-mini-list">
                    @forelse ($project->requirements as $requirement)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $requirement->code ? $requirement->code.' — ' : '' }}{{ $requirement->title }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ \App\Models\ProjectRequirement::TYPES[$requirement->requirement_type] }} &middot; v{{ $requirement->version }}
                                    @if ($requirement->client_approved) &middot; client approved @endif
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ str_replace('_', '-', $requirement->status) }}">{{ \App\Models\ProjectRequirement::STATUSES[$requirement->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No requirements captured.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Bugs & Issues</h2></div>
                <div class="admin-mini-list">
                    @forelse ($project->bugs as $bug)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $bug->bug_number }} — {{ $bug->title }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ \App\Models\Bug::SEVERITIES[$bug->severity] }}
                                    @if ($bug->assignee) &middot; {{ $bug->assignee->name }}@endif
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ str_replace('_', '-', $bug->status) }}">{{ \App\Models\Bug::STATUSES[$bug->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No bugs reported.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Change Requests</h2></div>
                <div class="admin-mini-list">
                    @forelse ($project->changeRequests as $request)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $request->request_number }} — {{ $request->title }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ money($request->cost_impact, $project->currency) }} &middot;
                                    {{ $request->timeline_impact_days > 0 ? '+' : '' }}{{ $request->timeline_impact_days }} days
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ str_replace('_', '-', $request->status) }}">{{ \App\Models\ChangeRequest::STATUSES[$request->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No change requests.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Recent Timesheets</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Date</th><th>Employee</th><th>Hours</th><th>Billable</th><th>Description</th></tr></thead>
                        <tbody>
                            @forelse ($project->timesheets as $timesheet)
                                <tr>
                                    <td>{{ $timesheet->date?->format('M d, Y') }}</td>
                                    <td>{{ $timesheet->employee?->name }}</td>
                                    <td>{{ $timesheet->hours }}</td>
                                    <td>
                                        <span class="admin-badge {{ $timesheet->is_billable ? 'admin-badge--active' : 'admin-badge--neutral' }}">
                                            {{ $timesheet->is_billable ? 'Billable' : 'Internal' }}
                                        </span>
                                    </td>
                                    <td class="admin-cell-muted">{{ Str::limit($timesheet->description, 60) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-state">No time logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Details</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Type</dt><dd>{{ $project->type ?: '—' }}</dd></div>
                        <div><dt>Priority</dt><dd><span class="admin-badge admin-badge--{{ $project->priority }}">{{ \App\Models\Project::PRIORITIES[$project->priority] }}</span></dd></div>
                        <div><dt>Project Manager</dt><dd>{{ $project->manager?->name ?? '—' }}</dd></div>
                        <div><dt>Start Date</dt><dd>{{ $project->start_date?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Deadline</dt><dd>{{ $project->deadline?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Completed</dt><dd>{{ $project->completed_at?->format('M d, Y') ?? '—' }}</dd></div>
                        @if ($project->quotation)
                            <div><dt>From Quotation</dt><dd><a href="{{ route('admin.quotations.show', $project->quotation) }}">{{ $project->quotation->quotation_number }}</a></dd></div>
                        @endif
                    </dl>

                    @if (is_array($project->technologies) && $project->technologies)
                        <div style="margin-top:14px">
                            <dt style="font-size:11px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--admin-muted); margin-bottom:5px;">Technologies</dt>
                            @foreach ($project->technologies as $technology)
                                <span class="admin-chip">{{ $technology }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Team</h2></div>
                <div class="admin-mini-list">
                    @forelse ($project->members as $member)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $member->name }}</div>
                                <div class="admin-mini-list__meta">{{ $member->pivot->role ?: $member->designation }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No team members assigned.</div>
                    @endforelse
                </div>
            </div>

            @if ($project->repository_url || $project->staging_url || $project->live_url)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Links</h2></div>
                    <div class="admin-mini-list">
                        @foreach (['Repository' => $project->repository_url, 'Staging' => $project->staging_url, 'Live' => $project->live_url] as $label => $url)
                            @if ($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="admin-mini-list__item">
                                    <div><div class="admin-mini-list__title">{{ $label }}</div>
                                        <div class="admin-mini-list__meta">{{ Str::limit($url, 40) }}</div></div>
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($project->invoices->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Invoices</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($project->invoices as $invoice)
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $invoice->invoice_number }}</div>
                                    <div class="admin-mini-list__meta">{{ money($invoice->total, $invoice->currency) }}</div></div>
                                <span class="admin-badge admin-badge--{{ $invoice->status }}">{{ \App\Models\Invoice::STATUSES[$invoice->status] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($project->attachments->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Attachments</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($project->attachments as $attachment)
                            <a href="{{ uploaded_url($attachment->path) }}" target="_blank" rel="noopener" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $attachment->original_name }}</div>
                                    <div class="admin-mini-list__meta">{{ $attachment->humanSize() }}</div></div>
                                <i class="fas fa-download"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($project->description || $project->notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Description</h2></div>
                    <div class="admin-card__body">
                        @if ($project->description)<div style="white-space:pre-line">{{ $project->description }}</div>@endif
                        @if ($project->notes)
                            <div class="admin-alert-inline admin-alert-inline--warning" style="margin-top:12px">
                                <i class="fas fa-eye-slash"></i> <span>{{ $project->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
