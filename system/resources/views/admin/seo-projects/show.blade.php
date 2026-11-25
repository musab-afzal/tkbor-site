@extends('admin.layouts.admin')

@section('title', $seoProject->name)
@section('page-title', 'SEO Project')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $seoProject->name }}
                <span class="admin-badge admin-badge--{{ $seoProject->status }}">{{ \App\Models\SeoProject::STATUSES[$seoProject->status] }}</span>
            </h1>
            <p>
                <a href="{{ $seoProject->website }}" target="_blank" rel="noopener">{{ $seoProject->website }}</a>
                @if ($seoProject->client) &middot; {{ $seoProject->client->displayName() }}@endif
            </p>
        </div>
        <div class="admin-btn-group">
            @can('keywords.create')
                <a href="{{ route('admin.keywords.create') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-plus"></i> Add Keyword</a>
            @endcan
            @can('seo_projects.edit')
                <a href="{{ route('admin.seo-projects.edit', $seoProject) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.seo-projects.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    @php
        $ranking = $seoProject->keywords->whereNotNull('current_rank');
        $topTen = $ranking->where('current_rank', '<=', 10);
        $improved = $seoProject->keywords->filter(fn ($keyword) => ($keyword->rankChange() ?? 0) > 0);
    @endphp

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Tracked Keywords</div><div class="admin-kpi__value">{{ $seoProject->keywords->count() }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--primary"><i class="fas fa-key"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Ranking</div><div class="admin-kpi__value">{{ $ranking->count() }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-chart-simple"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">In Top 10</div><div class="admin-kpi__value">{{ $topTen->count() }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-trophy"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Improved</div><div class="admin-kpi__value">{{ $improved->count() }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-arrow-trend-up"></i></div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Keywords</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Keyword</th><th>Target URL</th><th>Volume</th><th>Rank</th><th>Change</th><th>Checked</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($seoProject->keywords as $keyword)
                                <tr>
                                    <td>{{ $keyword->keyword }}</td>
                                    <td class="admin-cell-muted">{{ $keyword->target_url ? Str::limit($keyword->target_url, 30) : '—' }}</td>
                                    <td>{{ $keyword->search_volume ? number_format($keyword->search_volume) : '—' }}</td>
                                    <td>{{ $keyword->current_rank ?? '—' }}</td>
                                    <td>
                                        @php $change = $keyword->rankChange(); @endphp
                                        @if ($change === null)
                                            <span class="admin-cell-muted">—</span>
                                        @elseif ($change > 0)
                                            <span class="admin-badge admin-badge--success"><i class="fas fa-arrow-up"></i> {{ $change }}</span>
                                        @elseif ($change < 0)
                                            <span class="admin-badge admin-badge--danger"><i class="fas fa-arrow-down"></i> {{ abs($change) }}</span>
                                        @else
                                            <span class="admin-badge admin-badge--neutral">0</span>
                                        @endif
                                    </td>
                                    <td>{{ $keyword->last_checked_at?->format('M d, Y') ?? '—' }}</td>
                                    <td><span class="admin-badge admin-badge--{{ $keyword->status }}">{{ \App\Models\Keyword::STATUSES[$keyword->status] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="admin-empty-state">No keywords tracked yet.</td></tr>
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
                        <div><dt>Client</dt><dd>{{ $seoProject->client?->displayName() ?? '—' }}</dd></div>
                        <div><dt>Target Region</dt><dd>{{ $seoProject->target_country ?: '—' }}</dd></div>
                        <div><dt>Managed By</dt><dd>{{ $seoProject->manager?->name ?? '—' }}</dd></div>
                        <div><dt>Started</dt><dd>{{ $seoProject->start_date?->format('M d, Y') ?? '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            @if ($seoProject->goals)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Goals</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $seoProject->goals }}</div></div>
                </div>
            @endif

            @if ($seoProject->notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Notes</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $seoProject->notes }}</div></div>
                </div>
            @endif
        </div>
    </div>

@endsection
