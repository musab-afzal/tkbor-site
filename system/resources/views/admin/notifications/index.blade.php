@extends('admin.layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Notifications</h1>
            <p>{{ $unreadCount }} unread of {{ $notifications->total() }} total.</p>
        </div>
        @if ($unreadCount > 0)
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="admin-btn admin-btn--ghost"><i class="fas fa-check-double"></i> Mark all read</button>
            </form>
        @endif
    </div>

    <div class="admin-tabs">
        <a href="{{ route('admin.notifications.index') }}" class="admin-tab {{ ! request('unread') ? 'is-active' : '' }}">All</a>
        <a href="{{ route('admin.notifications.index', ['unread' => 1]) }}" class="admin-tab {{ request('unread') ? 'is-active' : '' }}">
            Unread <span class="admin-tab__count">{{ $unreadCount }}</span>
        </a>
    </div>

    <div class="admin-card">
        <div class="admin-mini-list">
            @forelse ($notifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="admin-mini-list__item {{ $notification->read_at ? '' : 'is-unread' }}">
                    <div>
                        <div class="admin-mini-list__title">
                            <i class="fas {{ $data['icon'] ?? 'fa-circle-info' }}"></i>
                            @if (! empty($data['url']))
                                <a href="{{ route('admin.notifications.read', $notification->id) }}">{{ $data['title'] ?? 'Notification' }}</a>
                            @else
                                {{ $data['title'] ?? 'Notification' }}
                            @endif
                            @unless ($notification->read_at)<span class="admin-badge admin-badge--new">New</span>@endunless
                        </div>
                        <div class="admin-mini-list__meta">
                            {{ $data['message'] ?? '' }} &middot; {{ $notification->created_at?->diffForHumans() }}
                        </div>
                    </div>
                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="js-delete-form" data-confirm="Remove this notification?">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Remove"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            @empty
                <div class="admin-mini-list__empty">You have no notifications.</div>
            @endforelse
        </div>

        <div class="admin-pagination-wrapper">{{ $notifications->links() }}</div>
    </div>

@endsection
