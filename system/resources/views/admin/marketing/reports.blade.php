@extends('admin.layouts.admin')

@section('title', 'Marketing Reports')
@section('page-title', 'Marketing Reports')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Marketing Reports</h1>
            <p>{{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</p>
        </div>
        <button type="button" onclick="window.print()" class="admin-btn admin-btn--ghost"><i class="fas fa-print"></i> Print</button>
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.marketing.reports') }}" method="GET" class="admin-filter-form">
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
                <a href="{{ route('admin.marketing.reports') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Campaign Budget</div><div class="admin-kpi__value">{{ money($totals['budget']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-sack-dollar"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Spent</div><div class="admin-kpi__value">{{ money($totals['spent']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--warning"><i class="fas fa-money-bill-wave"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Impressions</div><div class="admin-kpi__value">{{ number_format($totals['impressions']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--primary"><i class="fas fa-eye"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Clicks</div>
                <div class="admin-kpi__value">{{ number_format($totals['clicks']) }}</div>
                <div class="admin-kpi__meta">
                    {{ $totals['impressions'] > 0 ? round($totals['clicks'] / $totals['impressions'] * 100, 2) : 0 }}% CTR
                </div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-arrow-pointer"></i></div>
        </div>
        <div class="admin-kpi">
            <div>
                <div class="admin-kpi__label">Conversions</div>
                <div class="admin-kpi__value">{{ number_format($totals['conversions']) }}</div>
                <div class="admin-kpi__meta">
                    {{ $totals['conversions'] > 0 ? money($totals['spent'] / $totals['conversions']) : money(0) }} per conversion
                </div>
            </div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-bullseye"></i></div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__header"><h2>Campaign Performance</h2></div>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr><th>Campaign</th><th>Client</th><th>Platform</th><th>Budget</th><th>Spent</th><th>Impressions</th><th>Clicks</th><th>CTR</th><th>Conversions</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse ($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->name }}</td>
                            <td>{{ $campaign->client?->displayName() ?? '—' }}</td>
                            <td>{{ \App\Models\Campaign::PLATFORMS[$campaign->platform] ?? $campaign->platform }}</td>
                            <td>{{ money($campaign->budget, $campaign->currency) }}</td>
                            <td>{{ money($campaign->spent, $campaign->currency) }}</td>
                            <td>{{ number_format((int) $campaign->impressions) }}</td>
                            <td>{{ number_format((int) $campaign->clicks) }}</td>
                            <td>{{ $campaign->clickThroughRate() !== null ? $campaign->clickThroughRate().'%' : '—' }}</td>
                            <td>{{ number_format((int) $campaign->conversions) }}</td>
                            <td><span class="admin-badge admin-badge--{{ $campaign->status }}">{{ \App\Models\Campaign::STATUSES[$campaign->status] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="admin-empty-state">No campaigns in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-panel-grid">
        <div class="admin-card">
            <div class="admin-card__header"><h2>SEO Projects</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Project</th><th>Website</th><th>Keywords</th><th>Top 10</th></tr></thead>
                    <tbody>
                        @forelse ($seoProjects as $seoProject)
                            <tr>
                                <td><a href="{{ route('admin.seo-projects.show', $seoProject) }}">{{ $seoProject->name }}</a></td>
                                <td class="admin-cell-muted">{{ Str::limit($seoProject->website, 34) }}</td>
                                <td>{{ $seoProject->keywords_count }}</td>
                                <td><span class="admin-badge admin-badge--success">{{ $seoProject->ranking_keywords_count }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="admin-empty-state">No SEO projects.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2>Best Ranking Keywords</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Keyword</th><th>Project</th><th>Rank</th><th>Change</th></tr></thead>
                    <tbody>
                        @forelse ($topKeywords as $keyword)
                            <tr>
                                <td>{{ $keyword->keyword }}</td>
                                <td class="admin-cell-muted">{{ $keyword->seoProject?->name }}</td>
                                <td>{{ $keyword->current_rank }}</td>
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
                            </tr>
                        @empty
                            <tr><td colspan="4" class="admin-empty-state">No keywords tracked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2>Social Posts by Status</h2></div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Status</th><th>Posts</th></tr></thead>
                    <tbody>
                        @forelse (\App\Models\SocialPost::STATUSES as $status => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $postsByStatus[$status] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="admin-empty-state">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
