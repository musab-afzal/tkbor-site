@extends('admin.layouts.admin')

@section('title', 'Social Media Calendar')
@section('page-title', 'Social Media Calendar')

@push('styles')
<style>
    .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 1px; background: var(--admin-border); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-sm); overflow: hidden; }
    .calendar-head { background: #eef1f8; padding: 9px 8px; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--admin-muted); text-align: center; }
    .calendar-cell { background: #fff; min-height: 118px; padding: 7px; }
    .calendar-cell.is-outside { background: #fafbfd; }
    .calendar-cell.is-today { background: #f5f7ff; }
    .calendar-date { font-size: 12px; font-weight: 600; color: var(--admin-muted); margin-bottom: 5px; }
    .calendar-cell.is-today .calendar-date { color: var(--admin-primary); }
    .calendar-post { display: block; font-size: 11px; padding: 4px 6px; border-radius: 6px; margin-bottom: 4px; background: var(--admin-neutral-bg); color: var(--admin-text); }
    .calendar-post:hover { background: #e2e8f0; }
    @media (max-width: 780px) {
        .calendar-grid { grid-template-columns: 1fr; }
        .calendar-head { display: none; }
        .calendar-cell { min-height: 0; }
        .calendar-cell:empty, .calendar-cell.is-outside { display: none; }
    }
</style>
@endpush

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $month->format('F Y') }}</h1>
            <p>Everything scheduled across your social channels.</p>
        </div>
        <div class="admin-btn-group">
            <a href="{{ route('admin.social-posts.calendar', ['month' => $month->copy()->subMonth()->format('Y-m-d')]) }}" class="admin-btn admin-btn--ghost">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
            <a href="{{ route('admin.social-posts.calendar') }}" class="admin-btn admin-btn--ghost">Today</a>
            <a href="{{ route('admin.social-posts.calendar', ['month' => $month->copy()->addMonth()->format('Y-m-d')]) }}" class="admin-btn admin-btn--ghost">
                Next <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('admin.social-posts.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-list"></i> List</a>
            @can('social_posts.create')
                <a href="{{ route('admin.social-posts.create') }}" class="admin-btn admin-btn--primary"><i class="fas fa-plus"></i> New Post</a>
            @endcan
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__body">
            @php
                $start = $month->copy()->startOfMonth()->startOfWeek();
                $end = $month->copy()->endOfMonth()->endOfWeek();
                $cursor = $start->copy();
            @endphp

            <div class="calendar-grid">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                    <div class="calendar-head">{{ $weekday }}</div>
                @endforeach

                @while ($cursor <= $end)
                    @php
                        $key = $cursor->toDateString();
                        $dayPosts = $posts[$key] ?? collect();
                        $outside = $cursor->month !== $month->month;
                    @endphp

                    <div class="calendar-cell {{ $outside ? 'is-outside' : '' }} {{ $cursor->isToday() ? 'is-today' : '' }}">
                        <div class="calendar-date">{{ $cursor->format('j') }}</div>

                        @foreach ($dayPosts as $post)
                            <a href="{{ route('admin.social-posts.edit', $post) }}" class="calendar-post">
                                <strong>{{ $post->scheduled_at->format('H:i') }}</strong> {{ Str::limit($post->title, 22) }}
                                <div>
                                    <span class="admin-badge admin-badge--{{ $post->status }}" style="font-size:9px">
                                        {{ \App\Models\SocialPost::STATUSES[$post->status] }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @php $cursor->addDay(); @endphp
                @endwhile
            </div>
        </div>
    </div>

@endsection
