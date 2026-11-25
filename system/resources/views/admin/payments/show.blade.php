@extends('admin.layouts.admin')

@section('title', $payment->payment_number)
@section('page-title', 'Payment '.$payment->payment_number)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ money($payment->amount, $payment->currency) }}
                <span class="admin-badge admin-badge--{{ $payment->status }}">{{ \App\Models\Payment::STATUSES[$payment->status] }}</span>
            </h1>
            <p>{{ $payment->payment_number }} &middot; {{ $payment->payment_date?->format('M d, Y') }}</p>
        </div>
        <div class="admin-btn-group">
            @can('payments.edit')
                <a href="{{ route('admin.payments.edit', $payment) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.payments.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Receipt</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Client</dt><dd>
                            @if ($payment->client)
                                <a href="{{ route('admin.clients.show', $payment->client) }}">{{ $payment->client->displayName() }}</a>
                            @else — @endif
                        </dd></div>
                        <div><dt>Invoice</dt><dd>
                            @if ($payment->invoice)
                                <a href="{{ route('admin.invoices.show', $payment->invoice) }}">{{ $payment->invoice->invoice_number }}</a>
                            @else Unallocated advance @endif
                        </dd></div>
                        <div><dt>Amount</dt><dd>{{ money($payment->amount, $payment->currency) }}</dd></div>
                        <div><dt>Date</dt><dd>{{ $payment->payment_date?->format('M d, Y') }}</dd></div>
                        <div><dt>Method</dt><dd>{{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</dd></div>
                        <div><dt>Reference</dt><dd>{{ $payment->reference ?: '—' }}</dd></div>
                    </dl>

                    @if ($payment->notes)
                        <div style="margin-top:14px; white-space:pre-line">{{ $payment->notes }}</div>
                    @endif
                </div>
            </div>

            @if ($payment->invoice)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Invoice Position</h2></div>
                    <div class="admin-card__body">
                        <div class="admin-totals">
                            <div class="admin-totals__row"><span>Invoice total</span><span>{{ money($payment->invoice->total, $payment->invoice->currency) }}</span></div>
                            <div class="admin-totals__row"><span>Paid to date</span><span>{{ money($payment->invoice->paid_amount, $payment->invoice->currency) }}</span></div>
                            <div class="admin-totals__row admin-totals__row--grand"><span>Balance</span><span>{{ money($payment->invoice->balance, $payment->invoice->currency) }}</span></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            @if ($payment->receipt)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Attached Receipt</h2></div>
                    <div class="admin-card__body">
                        @if (\Illuminate\Support\Str::endsWith(strtolower($payment->receipt), ['.jpg', '.jpeg', '.png', '.webp']))
                            <img src="{{ uploaded_url($payment->receipt) }}" alt="Receipt" style="max-width:100%">
                        @else
                            <a href="{{ uploaded_url($payment->receipt) }}" target="_blank" rel="noopener" class="admin-btn admin-btn--ghost">
                                <i class="fas fa-file-pdf"></i> Open receipt
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
