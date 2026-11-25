@extends('admin.layouts.admin')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Invoices</h1>
            <p>What you have billed, what has been paid and what is still outstanding.</p>
        </div>
        @can('invoices.create')
            <a href="{{ route('admin.invoices.create') }}" class="admin-btn admin-btn--primary"><i class="fas fa-plus"></i> New Invoice</a>
        @endcan
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Total Invoiced</div><div class="admin-kpi__value">{{ money($totals['invoiced']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Received</div><div class="admin-kpi__value">{{ money($totals['received']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-circle-check"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Outstanding</div><div class="admin-kpi__value">{{ money($totals['outstanding']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--danger"><i class="fas fa-hand-holding-dollar"></i></div>
        </div>
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.invoices.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field admin-filter-field--search">
                <label class="admin-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="admin-input" value="{{ request('search') }}" placeholder="Invoice number or client...">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="status">Status</label>
                <select id="status" name="status" class="admin-select">
                    <option value="">All</option>
                    @foreach (\App\Models\Invoice::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="client_id">Client</label>
                <select id="client_id" name="client_id" class="admin-select">
                    <option value="">All</option>
                    @foreach ($clients as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('client_id') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="from">From</label>
                <input type="date" id="from" name="from" class="admin-input" value="{{ request('from') }}">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="to">To</label>
                <input type="date" id="to" name="to" class="admin-input" value="{{ request('to') }}">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="overdue">Overdue</label>
                <select id="overdue" name="overdue" class="admin-select">
                    <option value="">All</option>
                    <option value="1" @selected(request('overdue'))>Overdue only</option>
                </select>
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.invoices.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Invoice #</th><th>Client</th><th>Project</th><th>Date</th><th>Due</th>
                        <th>Total</th><th>Paid</th><th>Balance</th><th>Status</th>
                        <th class="admin-table__actions-head">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td><span class="admin-cell-code">{{ $invoice->invoice_number }}</span></td>
                            <td>{{ $invoice->client?->displayName() ?? '—' }}</td>
                            <td>{{ $invoice->project?->name ?? '—' }}</td>
                            <td>{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                            <td>
                                @if ($invoice->isOverdue())
                                    <span class="admin-badge admin-badge--overdue">{{ $invoice->due_date->format('M d, Y') }}</span>
                                @else
                                    {{ $invoice->due_date?->format('M d, Y') ?? '—' }}
                                @endif
                            </td>
                            <td>{{ money($invoice->total, $invoice->currency) }}</td>
                            <td>{{ money($invoice->paid_amount, $invoice->currency) }}</td>
                            <td>{{ money($invoice->balance, $invoice->currency) }}</td>
                            <td><span class="admin-badge admin-badge--{{ $invoice->status }}">{{ \App\Models\Invoice::STATUSES[$invoice->status] }}</span></td>
                            <td>
                                <div class="admin-row-actions">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="admin-icon-btn" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" rel="noopener" class="admin-icon-btn" title="Print"><i class="fas fa-print"></i></a>
                                    @can('invoices.edit')
                                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="admin-icon-btn" title="Edit"><i class="fas fa-pen"></i></a>
                                    @endcan
                                    @can('invoices.delete')
                                        <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" class="js-delete-form" data-confirm="Delete invoice {{ $invoice->invoice_number }}?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="admin-empty-state"><i class="fas fa-file-invoice-dollar"></i>No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination-wrapper">{{ $invoices->links() }}</div>
    </div>

@endsection
