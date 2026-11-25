<div class="admin-flash-stack">
    @if (session('success'))
        <div class="admin-alert admin-alert--success" data-autodismiss>
            <i class="fas fa-circle-check"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="admin-alert-close">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="admin-alert admin-alert--danger" data-autodismiss>
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="admin-alert-close">&times;</button>
        </div>
    @endif
</div>
