@extends('admin.layouts.admin')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Audit Logs</h1>
            <p>Every create, update, delete and status change, with who did it and when.</p>
        </div>
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field admin-filter-field--search">
                <label class="admin-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="admin-input" value="{{ request('search') }}" placeholder="Description, record or user...">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="action">Action</label>
                <select id="action" name="action" class="admin-select">
                    <option value="">All</option>
                    @foreach ($actions as $value => $label)
                        <option value="{{ $value }}" @selected(request('action') === $value)>{{ ucfirst(str_replace('_', ' ', $label)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="subject_type">Record Type</label>
                <select id="subject_type" name="subject_type" class="admin-select">
                    <option value="">All</option>
                    @foreach ($subjects as $value => $label)
                        <option value="{{ $value }}" @selected(request('subject_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="admin_id">User</label>
                <select id="admin_id" name="admin_id" class="admin-select">
                    <option value="">Everyone</option>
                    @foreach ($admins as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('admin_id') === (string) $id)>{{ $name }}</option>
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
                <a href="{{ route('admin.audit-logs.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead><tr><th>When</th><th>User</th><th>Action</th><th>Record</th><th>Changes</th><th>IP</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <div class="admin-cell-stack">
                                    <span>{{ $log->created_at?->format('M d, Y') }}</span>
                                    <small>{{ $log->created_at?->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>{{ $log->admin_name ?? $log->admin?->name ?? 'System' }}</td>
                            <td><span class="admin-badge admin-badge--{{ $log->action === 'deleted' ? 'danger' : ($log->action === 'created' ? 'success' : 'info') }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span></td>
                            <td>
                                <div class="admin-cell-stack">
                                    <span>{{ $log->subject_label ?: '—' }}</span>
                                    <small>{{ $log->subject_type ? class_basename($log->subject_type) : '' }}</small>
                                </div>
                            </td>
                            <td class="admin-cell-muted">
                                @if (is_array($log->changes) && $log->changes)
                                    {{ Str::limit(collect($log->changes)->keys()->join(', '), 60) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="admin-cell-muted">{{ $log->ip_address ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="admin-empty-state"><i class="fas fa-clipboard-list"></i>No activity recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination-wrapper">{{ $logs->links() }}</div>
    </div>

@endsection
