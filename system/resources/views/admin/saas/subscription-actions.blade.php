{{-- Extra row actions on the SaaS subscription listing. Expects $record. --}}
@if ($record->payment_receipt)
    <a href="{{ uploaded_url($record->payment_receipt) }}" target="_blank" rel="noopener" class="admin-icon-btn" title="View payment receipt">
        <i class="fas fa-receipt"></i>
    </a>
@endif

@can('saas.approve')
    @if ($record->payment_status === 'pending')
        <form action="{{ route('admin.saas-subscriptions.approve', $record) }}" method="POST"
              onsubmit="return confirm('Approve this payment and extend access by one billing period?\nRef: {{ e($record->payment_reference) }}');">
            @csrf @method('PATCH')
            <button type="submit" class="admin-icon-btn" title="Approve payment ({{ $record->payment_reference }})" style="color:#16a34a;">
                <i class="fas fa-circle-check"></i>
            </button>
        </form>
        <form action="{{ route('admin.saas-subscriptions.reject', $record) }}" method="POST"
              onsubmit="return confirm('Reject this payment?');">
            @csrf @method('PATCH')
            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Reject payment">
                <i class="fas fa-ban"></i>
            </button>
        </form>
    @endif
@endcan

@can('saas.edit')
    @if ($record->hasAccess())
        <form action="{{ route('admin.saas-subscriptions.expire', $record) }}" method="POST"
              onsubmit="return confirm('End access now? The customer will lose the login details immediately.');">
            @csrf @method('PATCH')
            <button type="submit" class="admin-icon-btn" title="End access now">
                <i class="fas fa-hourglass-end"></i>
            </button>
        </form>
    @endif
@endcan
