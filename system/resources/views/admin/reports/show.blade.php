@extends('admin.layouts.admin')

@section('title', $meta['label'].' Report')
@section('page-title', $meta['label'].' Report')

@push('styles')
<style media="print">
    .admin-sidebar, .admin-topbar, .admin-tabs, .admin-filter-card, .admin-page-header .admin-btn-group { display: none !important; }
    .admin-main { margin-left: 0 !important; }
    .admin-content { padding: 0 !important; }
</style>
@endpush

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $meta['label'] }}</h1>
            <p>{{ $meta['description'] }} &middot; {{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</p>
        </div>
        <div class="admin-btn-group">
            <button type="button" onclick="window.print()" class="admin-btn admin-btn--ghost"><i class="fas fa-print"></i> Print</button>
            @can('reports.export')
                <a href="{{ route('admin.reports.show', array_merge([$report], request()->query(), ['export' => 1])) }}" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            @endcan
            <a href="{{ route('admin.reports.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> All Reports</a>
        </div>
    </div>

    <div class="admin-tabs">
        @foreach ($reports as $key => $item)
            <a href="{{ route('admin.reports.show', $key) }}" class="admin-tab {{ $report === $key ? 'is-active' : '' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.reports.show', $report) }}" method="GET" class="admin-filter-form">
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
                <a href="{{ route('admin.reports.show', $report) }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    @if ($summary)
        <div class="admin-kpi-grid">
            @foreach ($summary as $label => $value)
                <div class="admin-kpi">
                    <div>
                        <div class="admin-kpi__label">{{ $label }}</div>
                        <div class="admin-kpi__value">{{ $value }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>@foreach ($headings as $heading)<th>{{ $heading }}</th>@endforeach</tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>@foreach ($row as $cell)<td>{{ $cell !== null && $cell !== '' ? $cell : '—' }}</td>@endforeach</tr>
                    @empty
                        <tr>
                            <td colspan="{{ max(1, count($headings)) }}" class="admin-empty-state">
                                <i class="fas {{ $meta['icon'] }}"></i>No data for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
