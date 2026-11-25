@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Welcome back, {{ auth('admin')->user()->name }}</h1>
            <p>Everything happening across the agency right now.</p>
        </div>
    </div>

    @php
        $kpiCards = [
            ['label' => 'Total Leads', 'value' => number_format($kpis['total_leads']), 'icon' => 'fa-bullseye', 'tone' => 'primary', 'route' => 'admin.leads.index', 'can' => 'leads.view'],
            ['label' => 'New Leads Today', 'value' => number_format($kpis['new_leads_today']), 'icon' => 'fa-user-plus', 'tone' => 'info', 'route' => 'admin.leads.index', 'can' => 'leads.view'],
            ['label' => 'Total Clients', 'value' => number_format($kpis['total_clients']), 'icon' => 'fa-user-tie', 'tone' => 'primary', 'route' => 'admin.clients.index', 'can' => 'clients.view'],
            ['label' => 'Active Clients', 'value' => number_format($kpis['active_clients']), 'icon' => 'fa-user-check', 'tone' => 'success', 'route' => 'admin.clients.index', 'can' => 'clients.view'],
            ['label' => 'Active Projects', 'value' => number_format($kpis['active_projects']), 'icon' => 'fa-diagram-project', 'tone' => 'info', 'route' => 'admin.projects.index', 'can' => 'projects.view'],
            ['label' => 'Completed Projects', 'value' => number_format($kpis['completed_projects']), 'icon' => 'fa-circle-check', 'tone' => 'success', 'route' => 'admin.projects.index', 'can' => 'projects.view'],
            ['label' => 'Pending Quotations', 'value' => number_format($kpis['pending_quotations']), 'icon' => 'fa-file-invoice', 'tone' => 'warning', 'route' => 'admin.quotations.index', 'can' => 'quotations.view'],
            ['label' => 'Approved Quotations', 'value' => number_format($kpis['approved_quotations']), 'icon' => 'fa-file-circle-check', 'tone' => 'success', 'route' => 'admin.quotations.index', 'can' => 'quotations.view'],
            ['label' => 'Pending Payments', 'value' => money($kpis['pending_payments']), 'icon' => 'fa-hand-holding-dollar', 'tone' => 'danger', 'route' => 'admin.invoices.index', 'can' => 'invoices.view', 'meta' => $kpis['overdue_invoices'].' overdue invoices'],
            ['label' => 'Monthly Revenue', 'value' => money($kpis['monthly_revenue']), 'icon' => 'fa-arrow-trend-up', 'tone' => 'success', 'route' => 'admin.payments.index', 'can' => 'payments.view', 'meta' => now()->format('F Y')],
            ['label' => 'Monthly Expenses', 'value' => money($kpis['monthly_expenses']), 'icon' => 'fa-arrow-trend-down', 'tone' => 'warning', 'route' => 'admin.expenses.index', 'can' => 'expenses.view', 'meta' => now()->format('F Y')],
            ['label' => 'Tasks Due Today', 'value' => number_format($kpis['tasks_due_today']), 'icon' => 'fa-calendar-day', 'tone' => 'info', 'route' => 'admin.tasks.due-today', 'can' => 'tasks.view'],
            ['label' => 'Upcoming Deadlines', 'value' => number_format($kpis['upcoming_deadlines']), 'icon' => 'fa-flag-checkered', 'tone' => 'warning', 'route' => 'admin.projects.index', 'can' => 'projects.view', 'meta' => 'Next 14 days'],
            ['label' => 'Overdue Tasks', 'value' => number_format($kpis['overdue_tasks']), 'icon' => 'fa-triangle-exclamation', 'tone' => 'danger', 'route' => 'admin.tasks.overdue', 'can' => 'tasks.view'],
            ['label' => 'Open Support Tickets', 'value' => number_format($kpis['open_tickets']), 'icon' => 'fa-headset', 'tone' => 'purple', 'route' => 'admin.tickets.index', 'can' => 'tickets.view'],
            ['label' => 'Website Inquiries', 'value' => number_format($kpis['website_inquiries']), 'icon' => 'fa-inbox', 'tone' => 'primary', 'route' => 'admin.inquiries.index', 'can' => 'inquiries.view', 'meta' => $kpis['new_inquiries'].' unread'],
        ];
    @endphp

    <div class="admin-kpi-grid">
        @foreach ($kpiCards as $card)
            @can($card['can'])
                <a href="{{ route($card['route']) }}" class="admin-kpi">
                    <div>
                        <div class="admin-kpi__label">{{ $card['label'] }}</div>
                        <div class="admin-kpi__value">{{ $card['value'] }}</div>
                        @isset($card['meta'])<div class="admin-kpi__meta">{{ $card['meta'] }}</div>@endisset
                    </div>
                    <div class="admin-kpi__icon admin-kpi__icon--{{ $card['tone'] }}"><i class="fas {{ $card['icon'] }}"></i></div>
                </a>
            @endcan
        @endforeach
    </div>

    <div class="admin-panel-grid">

        @can('tasks.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Tasks Due Today</h2>
                    <a href="{{ route('admin.tasks.due-today') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($tasksDueToday as $task)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">
                                    <a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a>
                                </div>
                                <div class="admin-mini-list__meta">
                                    {{ $task->project?->name ?? 'General' }}
                                    @if ($task->assignees->isNotEmpty()) &middot; {{ $task->assignees->pluck('name')->join(', ') }}@endif
                                    @if ($task->due_time) &middot; {{ \Illuminate\Support\Carbon::parse($task->due_time)->format('g:i A') }}@endif
                                </div>
                            </div>
                            <div class="admin-btn-group">
                                <span class="admin-badge admin-badge--{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }}</span>
                                @can('tasks.edit')
                                    <form action="{{ route('admin.tasks.complete', $task) }}" method="POST" class="admin-inline-form">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="admin-icon-btn" title="Mark complete"><i class="fas fa-check"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">Nothing due today.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Overdue Tasks</h2>
                    <a href="{{ route('admin.tasks.overdue') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($overdueTasks as $task)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title"><a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a></div>
                                <div class="admin-mini-list__meta">
                                    Due {{ $task->due_date?->format('M d, Y') }} &middot; {{ $task->project?->name ?? 'General' }}
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--overdue">{{ (int) $task->due_date?->diffInDays(today()) }}d late</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No overdue tasks.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Upcoming Tasks</h2>
                    <a href="{{ route('admin.tasks.upcoming') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($upcomingTasks as $task)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title"><a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a></div>
                                <div class="admin-mini-list__meta">Due {{ $task->due_date?->format('M d, Y') }}</div>
                            </div>
                            <span class="admin-badge admin-badge--{{ $task->status }}">{{ \App\Models\Task::STATUSES[$task->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">Nothing scheduled this week.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('leads.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Recent Leads</h2>
                    <a href="{{ route('admin.leads.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($recentLeads as $lead)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title"><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a></div>
                                <div class="admin-mini-list__meta">
                                    {{ $lead->company ?: $lead->email ?: '—' }}
                                    @if ($lead->assignee) &middot; {{ $lead->assignee->name }}@endif
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ str_replace('_', '-', $lead->status) }}">{{ \App\Models\Lead::STATUSES[$lead->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No leads yet.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('inquiries.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Recent Website Inquiries</h2>
                    <a href="{{ route('admin.inquiries.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($recentInquiries as $inquiry)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">
                                    <a href="{{ route('admin.inquiries.show', $inquiry) }}">{{ $inquiry->name }}</a>
                                    @unless ($inquiry->read_at)<span class="admin-badge admin-badge--new">New</span>@endunless
                                </div>
                                <div class="admin-mini-list__meta">
                                    {{ $inquiry->service ?: $inquiry->subject ?: 'General enquiry' }}
                                    &middot; {{ $inquiry->created_at?->diffForHumans() }}
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ $inquiry->status }}">{{ \App\Models\WebsiteInquiry::STATUSES[$inquiry->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No inquiries yet.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('quotations.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Recent Quotations</h2>
                    <a href="{{ route('admin.quotations.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($recentQuotations as $quotation)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title"><a href="{{ route('admin.quotations.show', $quotation) }}">{{ $quotation->quotation_number }}</a></div>
                                <div class="admin-mini-list__meta">{{ $quotation->client?->displayName() }} &middot; {{ money($quotation->grand_total, $quotation->currency) }}</div>
                            </div>
                            <span class="admin-badge admin-badge--{{ $quotation->status }}">{{ \App\Models\Quotation::STATUSES[$quotation->status] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No quotations yet.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('payments.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Recent Payments</h2>
                    <a href="{{ route('admin.payments.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($recentPayments as $payment)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $payment->client?->displayName() ?? '—' }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ $payment->invoice?->invoice_number ?? $payment->payment_number }}
                                    &middot; {{ $payment->payment_date?->format('M d, Y') }}
                                </div>
                            </div>
                            <strong>{{ money($payment->amount, $payment->currency) }}</strong>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No payments recorded.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('projects.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Upcoming Project Deadlines</h2>
                    <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($projectDeadlines as $project)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title"><a href="{{ route('admin.projects.show', $project) }}">{{ $project->name }}</a></div>
                                <div class="admin-mini-list__meta">{{ $project->client?->displayName() }} &middot; {{ $project->deadline?->format('M d, Y') }}</div>
                            </div>
                            @if ($project->isOverdue())
                                <span class="admin-badge admin-badge--overdue">Overdue</span>
                            @else
                                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $project->status) }}">{{ \App\Models\Project::STATUSES[$project->status] }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No deadlines coming up.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('follow_ups.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Follow-ups Due</h2>
                    <a href="{{ route('admin.follow-ups.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($dueFollowUps as $followUp)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $followUp->subjectName() }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ \App\Models\FollowUp::TYPES[$followUp->type] }} &middot; {{ $followUp->scheduled_at?->format('M d, g:i A') }}
                                </div>
                            </div>
                            @if ($followUp->isOverdue())
                                <span class="admin-badge admin-badge--overdue">Overdue</span>
                            @else
                                <span class="admin-badge admin-badge--pending">Pending</span>
                            @endif
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No follow-ups due.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('assets.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Renewals & Expiries</h2>
                    <a href="{{ route('admin.assets.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($expiringAssets as $asset)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $asset->name }}</div>
                                <div class="admin-mini-list__meta">
                                    {{ \App\Models\Asset::TYPES[$asset->type] }}
                                    @if ($asset->client) &middot; {{ $asset->client->displayName() }}@endif
                                    &middot; {{ $asset->expiry_date?->format('M d, Y') }}
                                </div>
                            </div>
                            @if ($asset->isExpired())
                                <span class="admin-badge admin-badge--expired">Expired</span>
                            @else
                                <span class="admin-badge admin-badge--warning">{{ $asset->daysUntilExpiry() }}d</span>
                            @endif
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">Nothing expiring soon.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('tickets.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Open Support Tickets</h2>
                    <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-mini-list">
                    @forelse ($openTickets as $ticket)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title"><a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->subject }}</a></div>
                                <div class="admin-mini-list__meta">{{ $ticket->ticket_number }} &middot; {{ $ticket->client?->displayName() ?? '—' }}</div>
                            </div>
                            <span class="admin-badge admin-badge--{{ $ticket->priority }}">{{ \App\Models\Ticket::PRIORITIES[$ticket->priority] }}</span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No open tickets.</div>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('workload.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Employee Workload</h2>
                    <a href="{{ route('admin.workload.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">View all</a>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Employee</th><th>Open Tasks</th><th>Overdue</th><th>Projects</th></tr></thead>
                        <tbody>
                            @forelse ($workload as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->open_tasks_count }}</td>
                                    <td>
                                        @if ($employee->overdue_tasks_count > 0)
                                            <span class="admin-badge admin-badge--overdue">{{ $employee->overdue_tasks_count }}</span>
                                        @else — @endif
                                    </td>
                                    <td>{{ $employee->active_projects_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="admin-empty-state">No employees yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endcan

    </div>

    <div class="admin-panel-grid admin-panel-grid--wide">
        @can('finance_reports.view')
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Revenue vs Expenses</h2>
                    <a href="{{ route('admin.finance.reports') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Financial reports</a>
                </div>
                @php
                    // Bars are scaled against the largest figure in the window, so an
                    // empty month simply renders as a hairline rather than breaking.
                    $chartPeak = collect($monthly)
                        ->flatMap(fn ($month) => [$month['revenue'], $month['expenses']])
                        ->max() ?: 1;
                @endphp

                <div class="admin-chart-legend">
                    <span><i class="is-revenue"></i> Revenue</span>
                    <span><i class="is-expense"></i> Expenses</span>
                </div>

                <div class="admin-chart">
                    @foreach ($monthly as $month)
                        <div class="admin-chart__month">
                            <div class="admin-chart__bars">
                                <div class="admin-chart__bar admin-chart__bar--revenue"
                                     style="height: {{ max(1, round(($month['revenue'] / $chartPeak) * 100)) }}%"
                                     title="{{ $month['label'] }} revenue: {{ money($month['revenue']) }}"></div>
                                <div class="admin-chart__bar admin-chart__bar--expense"
                                     style="height: {{ max(1, round(($month['expenses'] / $chartPeak) * 100)) }}%"
                                     title="{{ $month['label'] }} expenses: {{ money($month['expenses']) }}"></div>
                            </div>
                            <div class="admin-chart__label">{{ \Illuminate\Support\Str::before($month['label'], ' ') }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Month</th><th>Revenue</th><th>Expenses</th><th>Profit</th></tr></thead>
                        <tbody>
                            @foreach ($monthly as $month)
                                <tr>
                                    <td>{{ $month['label'] }}</td>
                                    <td>{{ money($month['revenue']) }}</td>
                                    <td>{{ money($month['expenses']) }}</td>
                                    <td>
                                        <span class="admin-badge {{ $month['profit'] >= 0 ? 'admin-badge--success' : 'admin-badge--danger' }}">
                                            {{ money($month['profit']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endcan

        <div class="admin-card">
            <div class="admin-card__header">
                <h2>Recent Activity</h2>
                @can('audit_logs.view')
                    <a href="{{ route('admin.audit-logs.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Audit log</a>
                @endcan
            </div>
            <div class="admin-card__body">
                <div class="admin-timeline">
                    @forelse ($activities as $activity)
                        <div class="admin-timeline__item">
                            <div class="admin-timeline__title">{{ $activity->description }}</div>
                            <div class="admin-timeline__meta">
                                {{ $activity->admin_name ?? 'System' }} &middot; {{ $activity->created_at?->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
