@extends('admin.layouts.admin')

@section('title', $invoice->invoice_number)
@section('page-title', 'Invoice '.$invoice->invoice_number)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $invoice->invoice_number }}
                <span class="admin-badge admin-badge--{{ $invoice->status }}">{{ \App\Models\Invoice::STATUSES[$invoice->status] }}</span>
                @if ($invoice->isOverdue())<span class="admin-badge admin-badge--overdue">Overdue</span>@endif
            </h1>
            <p><a href="{{ route('admin.clients.show', $invoice->client) }}">{{ $invoice->client?->displayName() }}</a></p>
        </div>
        <div class="admin-btn-group">
            <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" rel="noopener" class="admin-btn admin-btn--ghost">
                <i class="fas fa-print"></i> Print / PDF
            </a>
            @can('payments.create')
                <a href="{{ route('admin.payments.create') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-money-bill-transfer"></i> Record Payment</a>
            @endcan
            @can('invoices.edit')
                @if ($invoice->status === \App\Models\Invoice::STATUS_DRAFT)
                    <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" class="admin-inline-form">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn--ghost"><i class="fas fa-paper-plane"></i> Mark as Sent</button>
                    </form>
                @endif
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.invoices.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Items</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td>
                                        <div class="admin-cell-stack">
                                            <span>{{ $item->name }}</span>
                                            @if ($item->description)<small>{{ $item->description }}</small>@endif
                                        </div>
                                    </td>
                                    <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                                    <td>{{ money($item->unit_price, $invoice->currency) }}</td>
                                    <td>{{ money($item->line_total, $invoice->currency) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="admin-card__body">
                    <div class="admin-totals">
                        <div class="admin-totals__row"><span>Subtotal</span><span>{{ money($invoice->subtotal, $invoice->currency) }}</span></div>
                        <div class="admin-totals__row"><span>Discount</span><span>-{{ money($invoice->discount_amount, $invoice->currency) }}</span></div>
                        <div class="admin-totals__row"><span>Tax</span><span>{{ money($invoice->tax_amount, $invoice->currency) }}</span></div>
                        <div class="admin-totals__row admin-totals__row--grand"><span>Total</span><span>{{ money($invoice->total, $invoice->currency) }}</span></div>
                        <div class="admin-totals__row"><span>Paid</span><span>{{ money($invoice->paid_amount, $invoice->currency) }}</span></div>
                        <div class="admin-totals__row"><span>Balance Due</span><span>{{ money($invoice->balance, $invoice->currency) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Payments</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Payment #</th><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($invoice->payments as $payment)
                                <tr>
                                    <td><a href="{{ route('admin.payments.show', $payment) }}" class="admin-cell-code">{{ $payment->payment_number }}</a></td>
                                    <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                                    <td>{{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</td>
                                    <td>{{ $payment->reference ?: '—' }}</td>
                                    <td>{{ money($payment->amount, $payment->currency) }}</td>
                                    <td><span class="admin-badge admin-badge--{{ $payment->status }}">{{ \App\Models\Payment::STATUSES[$payment->status] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="admin-empty-state">No payments recorded against this invoice.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($invoice->notes || $invoice->terms)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Notes & Terms</h2></div>
                    <div class="admin-card__body">
                        @if ($invoice->notes)<div style="white-space:pre-line; margin-bottom:12px">{{ $invoice->notes }}</div>@endif
                        @if ($invoice->terms)<div style="white-space:pre-line" class="admin-cell-muted">{{ $invoice->terms }}</div>@endif
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Details</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Invoice Date</dt><dd>{{ $invoice->invoice_date?->format('M d, Y') }}</dd></div>
                        <div><dt>Due Date</dt><dd>{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Currency</dt><dd>{{ $invoice->currency }}</dd></div>
                        <div><dt>Project</dt><dd>
                            @if ($invoice->project)
                                <a href="{{ route('admin.projects.show', $invoice->project) }}">{{ $invoice->project->name }}</a>
                            @else — @endif
                        </dd></div>
                        <div><dt>Quotation</dt><dd>
                            @if ($invoice->quotation)
                                <a href="{{ route('admin.quotations.show', $invoice->quotation) }}">{{ $invoice->quotation->quotation_number }}</a>
                            @else — @endif
                        </dd></div>
                        <div><dt>Milestone</dt><dd>{{ $invoice->milestone?->title ?? '—' }}</dd></div>
                        <div><dt>Sent</dt><dd>{{ $invoice->sent_at?->format('M d, Y') ?? 'Not sent' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

@endsection
