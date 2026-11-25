{{-- Sits inside the existing topbar; the surrounding markup is unchanged. --}}
<div class="admin-notif-wrap">
    <button type="button" class="admin-notif-btn" data-notif-toggle aria-label="Notifications">
        <i class="fas fa-bell"></i>
        @if (($unreadNotificationCount ?? 0) > 0)
            <span class="admin-notif-badge">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
        @endif
    </button>

    <div class="admin-notif-panel" data-notif-panel>
        <div class="admin-notif-panel__header">
            <span>Notifications</span>
            @if (($unreadNotificationCount ?? 0) > 0)
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Mark all read</button>
                </form>
            @endif
        </div>

        @forelse ($recentNotifications ?? [] as $notification)
            @php $data = $notification->data; @endphp
            <a href="{{ $data['url'] ?? route('admin.notifications.index') }}"
               class="admin-notif-item {{ $notification->read_at ? '' : 'is-unread' }}">
                <div><i class="fas {{ $data['icon'] ?? 'fa-circle-info' }}"></i> {{ $data['title'] ?? 'Notification' }}</div>
                @isset($data['message'])
                    <div class="admin-notif-item__time">{{ $data['message'] }}</div>
                @endisset
                <div class="admin-notif-item__time">{{ $notification->created_at?->diffForHumans() }}</div>
            </a>
        @empty
            <div class="admin-mini-list__empty">You are all caught up.</div>
        @endforelse

        <a href="{{ route('admin.notifications.index') }}" class="admin-notif-item" style="text-align:center; font-weight:600;">
            View all notifications
        </a>
    </div>
</div>
