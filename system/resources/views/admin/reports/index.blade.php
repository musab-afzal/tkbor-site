@extends('admin.layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Reports</h1>
            <p>Filter by date, then print or export any report to CSV.</p>
        </div>
    </div>

    <div class="admin-kpi-grid">
        @foreach ($reports as $key => $report)
            <a href="{{ route('admin.reports.show', $key) }}" class="admin-kpi">
                <div>
                    <div class="admin-kpi__label">{{ $report['label'] }}</div>
                    <div class="admin-kpi__meta" style="margin-top:6px">{{ $report['description'] }}</div>
                </div>
                <div class="admin-kpi__icon admin-kpi__icon--primary"><i class="fas {{ $report['icon'] }}"></i></div>
            </a>
        @endforeach
    </div>

@endsection
