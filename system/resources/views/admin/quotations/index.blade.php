@extends('admin.layouts.admin')

@section('title', 'Quotations')
@section('page-title', 'Quotations')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Quotations</h1>
            <p>Proposals sent to clients, their revisions and their outcome.</p>
        </div>
        @can('quotations.create')
            <a href="{{ route('admin.quotations.create') }}" class="admin-btn admin-btn--primary">
                <i class="fas fa-plus"></i> New Quotation
            </a>
        @endcan
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.quotations.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field admin-filter-field--search">
                <label class="admin-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="admin-input" value="{{ request('search') }}"
                       placeholder="Quotation number, project or client...">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="status">Status</label>
                <select id="status" name="status" class="admin-select">
                    <option value="">All</option>
                    @foreach (\App\Models\Quotation::STATUSES as $value => $label)
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
            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.quotations.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Quotation #</th>
                        <th>Client</th>
                        <th>Project</th>
                        <th>Date</th>
                        <th>Valid Until</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="admin-table__actions-head">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotations as $quotation)
                        <tr>
                            <td>
                                <span class="admin-cell-code">{{ $quotation->quotation_number }}</span>
                                @if ($quotation->revision > 0)
                                    <span class="admin-chip">Rev {{ $quotation->revision }}</span>
                                @endif
                            </td>
                            <td>{{ $quotation->client?->displayName() ?? '—' }}</td>
                            <td>{{ $quotation->project_name }}</td>
                            <td>{{ $quotation->quotation_date?->format('M d, Y') }}</td>
                            <td>
                                {{ $quotation->valid_until?->format('M d, Y') ?? '—' }}
                                @if ($quotation->isExpired())
                                    <span class="admin-badge admin-badge--expired">Expired</span>
                                @endif
                            </td>
                            <td>{{ money($quotation->grand_total, $quotation->currency) }}</td>
                            <td>
                                <span class="admin-badge admin-badge--{{ $quotation->status }}">
                                    {{ \App\Models\Quotation::STATUSES[$quotation->status] ?? $quotation->status }}
                                </span>
                                @if ($quotation->converted_project_id)
                                    <span class="admin-badge admin-badge--converted">Project</span>
                                @endif
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a href="{{ route('admin.quotations.show', $quotation) }}" class="admin-icon-btn" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.quotations.print', $quotation) }}" target="_blank" rel="noopener" class="admin-icon-btn" title="Print"><i class="fas fa-print"></i></a>
                                    @can('quotations.edit')
                                        @if ($quotation->isEditable())
                                            <a href="{{ route('admin.quotations.edit', $quotation) }}" class="admin-icon-btn" title="Edit"><i class="fas fa-pen"></i></a>
                                        @endif
                                    @endcan
                                    @can('quotations.delete')
                                        <form action="{{ route('admin.quotations.destroy', $quotation) }}" method="POST"
                                              class="js-delete-form" data-confirm="Delete quotation {{ $quotation->quotation_number }}?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="admin-empty-state"><i class="fas fa-file-invoice"></i>No quotations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination-wrapper">{{ $quotations->links() }}</div>
    </div>

@endsection
