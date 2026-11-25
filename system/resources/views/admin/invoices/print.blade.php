<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_number }} — {{ $settings->get('agency_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin-print.css') }}">
</head>
<body>

<div class="doc-toolbar no-print">
    <button type="button" onclick="window.print()" class="doc-btn doc-btn--primary">Print / Save as PDF</button>
    <a href="{{ route('admin.invoices.show', $invoice) }}" class="doc-btn">Back to invoice</a>
</div>

<div class="doc">
    <header class="doc-header">
        <div class="doc-brand">
            @if ($settings->get('agency_logo'))
                <img src="{{ uploaded_url($settings->get('agency_logo')) }}" alt="" class="doc-logo">
            @endif
            <div>
                <h1 class="doc-agency">{{ $settings->get('agency_name', config('app.name')) }}</h1>
                <div class="doc-agency-meta">
                    @if ($settings->get('agency_address')){{ $settings->get('agency_address') }}<br>@endif
                    @if ($settings->get('agency_phone')){{ $settings->get('agency_phone') }} &middot; @endif
                    @if ($settings->get('agency_email')){{ $settings->get('agency_email') }}@endif
                    @if ($settings->get('agency_website'))<br>{{ $settings->get('agency_website') }}@endif
                </div>
            </div>
        </div>

        <div class="doc-meta">
            <div class="doc-title">Invoice</div>
            <table class="doc-meta-table">
                <tr><th>Number</th><td>{{ $invoice->invoice_number }}</td></tr>
                <tr><th>Date</th><td>{{ $invoice->invoice_date?->format('M d, Y') }}</td></tr>
                @if ($invoice->due_date)
                    <tr><th>Due</th><td>{{ $invoice->due_date->format('M d, Y') }}</td></tr>
                @endif
                <tr><th>Status</th><td>{{ \App\Models\Invoice::STATUSES[$invoice->status] ?? $invoice->status }}</td></tr>
            </table>
        </div>
    </header>

    <section class="doc-parties">
        <div>
            <div class="doc-label">Bill To</div>
            <div class="doc-party-name">{{ $invoice->client?->displayName() }}</div>
            <div class="doc-party-meta">
                @if ($invoice->client?->contact_person){{ $invoice->client->contact_person }}<br>@endif
                @if ($invoice->client?->address){{ $invoice->client->address }}<br>@endif
                @if ($invoice->client?->city){{ $invoice->client->city }}@endif
                @if ($invoice->client?->country), {{ $invoice->client->country }}@endif
                @if ($invoice->client?->email)<br>{{ $invoice->client->email }}@endif
                @if ($invoice->client?->tax_number)<br>{{ $invoice->client->tax_name ?: 'Tax' }}: {{ $invoice->client->tax_number }}@endif
            </div>
        </div>
        <div>
            <div class="doc-label">Reference</div>
            <div class="doc-party-name">{{ $invoice->project?->name ?? 'Services rendered' }}</div>
            <div class="doc-party-meta">
                @if ($invoice->quotation)Quotation {{ $invoice->quotation->quotation_number }}<br>@endif
                @if ($invoice->milestone)Milestone: {{ $invoice->milestone->title }}@endif
            </div>
        </div>
    </section>

    <section class="doc-section">
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width:56%">Description</th>
                    <th style="width:12%">Qty</th>
                    <th style="width:16%">Unit Price</th>
                    <th style="width:16%" class="doc-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if ($item->description)<div class="doc-item-desc">{{ $item->description }}</div>@endif
                        </td>
                        <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                        <td>{{ money($item->unit_price, $invoice->currency) }}</td>
                        <td class="doc-right">{{ money($item->line_total, $invoice->currency) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="doc-section doc-totals-section">
        <table class="doc-totals">
            <tr><th>Subtotal</th><td>{{ money($invoice->subtotal, $invoice->currency) }}</td></tr>
            @if ((float) $invoice->discount_amount > 0)
                <tr><th>Discount</th><td>-{{ money($invoice->discount_amount, $invoice->currency) }}</td></tr>
            @endif
            @if ((float) $invoice->tax_amount > 0)
                <tr><th>{{ $settings->get('tax_name', 'Tax') }} ({{ rtrim(rtrim(number_format((float) $invoice->tax_percentage, 2), '0'), '.') }}%)</th>
                    <td>{{ money($invoice->tax_amount, $invoice->currency) }}</td></tr>
            @endif
            <tr class="doc-grand"><th>Total</th><td>{{ money($invoice->total, $invoice->currency) }}</td></tr>
            @if ((float) $invoice->paid_amount > 0)
                <tr><th>Paid</th><td>{{ money($invoice->paid_amount, $invoice->currency) }}</td></tr>
                <tr><th>Balance Due</th><td>{{ money($invoice->balance, $invoice->currency) }}</td></tr>
            @endif
        </table>
    </section>

    @if ($invoice->payments->where('status', 'completed')->isNotEmpty())
        <section class="doc-section">
            <h2>Payments Received</h2>
            <table class="doc-table">
                <thead><tr><th style="width:25%">Date</th><th style="width:30%">Method</th><th style="width:25%">Reference</th><th style="width:20%" class="doc-right">Amount</th></tr></thead>
                <tbody>
                    @foreach ($invoice->payments->where('status', 'completed') as $payment)
                        <tr>
                            <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                            <td>{{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</td>
                            <td>{{ $payment->reference ?: '—' }}</td>
                            <td class="doc-right">{{ money($payment->amount, $payment->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    @if ($settings->get('bank_account_number') || $settings->get('bank_name'))
        <section class="doc-section">
            <h2>Payment Details</h2>
            <table class="doc-table">
                <tbody>
                    @if ($settings->get('bank_name'))<tr><th style="width:30%">Bank</th><td>{{ $settings->get('bank_name') }}</td></tr>@endif
                    @if ($settings->get('bank_account_title'))<tr><th>Account Title</th><td>{{ $settings->get('bank_account_title') }}</td></tr>@endif
                    @if ($settings->get('bank_account_number'))<tr><th>Account Number</th><td>{{ $settings->get('bank_account_number') }}</td></tr>@endif
                    @if ($settings->get('bank_iban'))<tr><th>IBAN</th><td>{{ $settings->get('bank_iban') }}</td></tr>@endif
                    @if ($settings->get('bank_swift'))<tr><th>SWIFT</th><td>{{ $settings->get('bank_swift') }}</td></tr>@endif
                </tbody>
            </table>
            @if ($settings->get('payment_instructions'))<p class="doc-note">{{ $settings->get('payment_instructions') }}</p>@endif
        </section>
    @endif

    @if (filled($invoice->notes))
        <section class="doc-section"><h2>Notes</h2><p>{{ $invoice->notes }}</p></section>
    @endif

    @if (filled($invoice->terms))
        <section class="doc-section"><h2>Terms</h2><p>{{ $invoice->terms }}</p></section>
    @endif

    <footer class="doc-footer">
        {{ $settings->get('agency_name', config('app.name')) }} &middot; {{ $invoice->invoice_number }}
        @if ($settings->get('agency_website')) &middot; {{ $settings->get('agency_website') }}@endif
    </footer>
</div>

</body>
</html>
