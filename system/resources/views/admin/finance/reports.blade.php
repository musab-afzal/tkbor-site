@extends('admin.layouts.admin')

@section('title', 'Financial Reports')
@section('page-title', 'Financial Reports')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Financial Reports</h1>
            <p>{{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</p>
        </div>
        <button type="button" onclick="window.print()" class="admin-btn admin-btn--ghost"><i class="fas fa-print"></i> Print</button>
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.finance.reports') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field">
                <label class="admin-label" for="from">From</label>
                <input type="date" id="from" name="from" class="admin-input" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="to">To</label>
                <input type="date" id="to" name="to" class="admin-input" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-filter"></i> Apply</button>
                <a href="{{ route('admin.finance.reports') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Revenue</div><div class="admin-kpi__value">{{ money($summary['revenue']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-arrow-trend-up"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Expenses</div><div class="admin-kpi__value">{{ money($summary['expenses']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--warning"><i class="fas fa-arrow-trend-down"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Profit</div>
                <div class="admin-kpi__value">{{ money($summary['profit']) }}</div>
                <div class="admin-kpi__meta">{{ $summary['margin'] }}% margin</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--{{ $summary['profit'] >= 0 ? 'success' : 'danger' }}"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Invoiced</div><div class="admin-kpi__value">{{ money($summary['invoiced']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Outstanding</div>
                <div class="admin-kpi__value">{{ money($summary['outstanding']) }}</div>
                <div class="admin-kpi__meta">{{ money($summary['overdue']) }} overdue</div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--danger"><i class="fas fa-hand-holding-dollar"></i></div>
        </div>
    </div>

    <div class="admin-panel-grid admin-panel-grid--wide">
        <div class="admin-card">
            <div class="admin-card__header"><h2>Monthly Revenue vs Expenses</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Period</th><th>Revenue</th><th>Expenses</th><th>Profit</th></tr></thead>
                    <tbody>
                        @forelse ($monthly as $row)
                            <tr>
                                <td>{{ $row['period'] }}</td>
                                <td>{{ money($row['revenue']) }}</td>
                                <td>{{ money($row['expenses']) }}</td>
                                <td><span class="admin-badge {{ $row['profit'] >= 0 ? 'admin-badge--success' : 'admin-badge--danger' }}">{{ money($row['profit']) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="admin-empty-state">No financial activity in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2>Expenses by Category</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Category</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($expensesByCategory as $row)
                            <tr>
                                <td>{{ $row->category?->name ?? 'Uncategorised' }}</td>
                                <td>{{ money($row->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="admin-empty-state">No expenses recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2>Client Profitability</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Client</th><th>Invoiced</th><th>Received</th><th>Outstanding</th></tr></thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td><a href="{{ route('admin.clients.show', $client) }}">{{ $client->displayName() }}</a></td>
                                <td>{{ money($client->invoiced) }}</td>
                                <td>{{ money($client->received) }}</td>
                                <td>{{ money($client->outstandingBalance()) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="admin-empty-state">No client billing in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2>Project Profitability</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Project</th><th>Budget</th><th>Invoiced</th><th>Received</th><th>Costs</th><th>Profit</th><th>Hours</th></tr></thead>
                    <tbody>
                        @forelse ($projects as $project)
                            @php $profit = (float) $project->received - (float) $project->spent; @endphp
                            <tr>
                                <td>
                                    <div class="admin-cell-stack">
                                        <a href="{{ route('admin.projects.show', $project) }}">{{ $project->name }}</a>
                                        <small>{{ $project->client?->displayName() }}</small>
                                    </div>
                                </td>
                                <td>{{ money($project->budget, $project->currency) }}</td>
                                <td>{{ money($project->invoiced, $project->currency) }}</td>
                                <td>{{ money($project->received, $project->currency) }}</td>
                                <td>{{ money($project->spent, $project->currency) }}</td>
                                <td><span class="admin-badge {{ $profit >= 0 ? 'admin-badge--success' : 'admin-badge--danger' }}">{{ money($profit, $project->currency) }}</span></td>
                                <td>{{ number_format((float) $project->logged_hours, 1) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="admin-empty-state">No projects yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2>Outstanding Invoices</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Invoice #</th><th>Client</th><th>Due</th><th>Balance</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($outstandingInvoices as $invoice)
                            <tr>
                                <td><a href="{{ route('admin.invoices.show', $invoice) }}" class="admin-cell-code">{{ $invoice->invoice_number }}</a></td>
                                <td>{{ $invoice->client?->displayName() }}</td>
                                <td>
                                    @if ($invoice->isOverdue())
                                        <span class="admin-badge admin-badge--overdue">{{ $invoice->due_date->format('M d, Y') }}</span>
                                    @else
                                        {{ $invoice->due_date?->format('M d, Y') ?? '—' }}
                                    @endif
                                </td>
                                <td>{{ money($invoice->balance, $invoice->currency) }}</td>
                                <td><span class="admin-badge admin-badge--{{ $invoice->status }}">{{ \App\Models\Invoice::STATUSES[$invoice->status] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="admin-empty-state">Nothing outstanding.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
