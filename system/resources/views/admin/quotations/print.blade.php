{{-- Client-facing A4 quotation. Internal cost, margin and internal notes are
     deliberately absent from this document. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quotation->quotation_number }} — {{ $settings->get('agency_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin-print.css') }}">
</head>
<body>

<div class="doc-toolbar no-print">
    <button type="button" onclick="window.print()" class="doc-btn doc-btn--primary">Print / Save as PDF</button>
    <a href="{{ route('admin.quotations.show', $quotation) }}" class="doc-btn">Back to quotation</a>
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
            <div class="doc-title">Quotation</div>
            <table class="doc-meta-table">
                <tr><th>Number</th><td>{{ $quotation->quotation_number }}</td></tr>
                <tr><th>Date</th><td>{{ $quotation->quotation_date?->format('M d, Y') }}</td></tr>
                @if ($quotation->valid_until)
                    <tr><th>Valid Until</th><td>{{ $quotation->valid_until->format('M d, Y') }}</td></tr>
                @endif
                @if ($quotation->project_type)
                    <tr><th>Type</th><td>{{ $quotation->project_type }}</td></tr>
                @endif
            </table>
        </div>
    </header>

    <section class="doc-parties">
        <div>
            <div class="doc-label">Prepared For</div>
            <div class="doc-party-name">{{ $quotation->client?->displayName() }}</div>
            <div class="doc-party-meta">
                @if ($quotation->client?->contact_person){{ $quotation->client->contact_person }}<br>@endif
                @if ($quotation->client?->address){{ $quotation->client->address }}<br>@endif
                @if ($quotation->client?->city){{ $quotation->client->city }}@endif
                @if ($quotation->client?->country), {{ $quotation->client->country }}@endif
                @if ($quotation->client?->email)<br>{{ $quotation->client->email }}@endif
                @if ($quotation->client?->phone)<br>{{ $quotation->client->phone }}@endif
            </div>
        </div>
        <div>
            <div class="doc-label">Project</div>
            <div class="doc-party-name">{{ $quotation->project_name }}</div>
            <div class="doc-party-meta">
                @if ($quotation->preparedBy)Prepared by {{ $quotation->preparedBy->name }}<br>@endif
                @if ($quotation->total_estimated_days){{ $quotation->total_estimated_days }} working days estimated @endif
            </div>
        </div>
    </section>

    @if (filled($quotation->project_summary))
        <section class="doc-section">
            <h2>Project Summary</h2>
            <p>{{ $quotation->project_summary }}</p>
        </section>
    @endif

    @if (filled($quotation->requirements))
        <section class="doc-section">
            <h2>Requirements</h2>
            <p>{{ $quotation->requirements }}</p>
        </section>
    @endif

    @if (filled($quotation->proposed_solution))
        <section class="doc-section">
            <h2>Proposed Solution</h2>
            <p>{{ $quotation->proposed_solution }}</p>
        </section>
    @endif

    @if (filled($quotation->scope))
        <section class="doc-section">
            <h2>Scope of Work</h2>
            <p>{{ $quotation->scope }}</p>
        </section>
    @endif

    @php $mainItems = $quotation->itemsInSection('main'); @endphp
    @if ($mainItems->isNotEmpty())
        <section class="doc-section">
            <h2>Modules &amp; Services</h2>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th style="width:52%">Item</th>
                        <th style="width:10%">Qty</th>
                        <th style="width:14%">Unit Price</th>
                        <th style="width:10%">Days</th>
                        <th style="width:14%" class="doc-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mainItems as $item)
                        <tr @class(['doc-optional' => $item->is_optional])>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if ($item->is_optional)<span class="doc-tag">Optional</span>@endif
                                @if ($item->description)<div class="doc-item-desc">{{ $item->description }}</div>@endif
                                @if ($item->features)
                                    <ul class="doc-features">
                                        @foreach (preg_split('/\r\n|\r|\n/', $item->features) as $feature)
                                            @if (trim($feature) !== '')<li>{{ trim($feature) }}</li>@endif
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                            <td>{{ money($item->unit_price, $quotation->currency) }}</td>
                            <td>{{ $item->estimated_days ?: '—' }}</td>
                            <td class="doc-right">
                                @if ($item->is_optional)
                                    <span class="doc-muted">{{ money($item->grossValue(), $quotation->currency) }}</span>
                                @else
                                    {{ money($item->line_total, $quotation->currency) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    @php $additionalItems = $quotation->itemsInSection('additional'); @endphp
    @if ($additionalItems->isNotEmpty())
        <section class="doc-section">
            <h2>Additional Services</h2>
            <table class="doc-table">
                <thead><tr><th style="width:62%">Item</th><th style="width:10%">Qty</th><th style="width:14%">Unit Price</th><th style="width:14%" class="doc-right">Total</th></tr></thead>
                <tbody>
                    @foreach ($additionalItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if ($item->is_optional)<span class="doc-tag">Optional</span>@endif
                                @if ($item->description)<div class="doc-item-desc">{{ $item->description }}</div>@endif
                            </td>
                            <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                            <td>{{ money($item->unit_price, $quotation->currency) }}</td>
                            <td class="doc-right">{{ money($item->is_optional ? $item->grossValue() : $item->line_total, $quotation->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    @php $freeItems = $quotation->itemsInSection('free')->merge($quotation->items->where('is_free', true))->unique('id'); @endphp
    @if ($freeItems->isNotEmpty())
        <section class="doc-section">
            <h2>Included Free of Charge</h2>
            <table class="doc-table">
                <thead><tr><th style="width:70%">Item</th><th style="width:15%">Value</th><th style="width:15%" class="doc-right">You Pay</th></tr></thead>
                <tbody>
                    @foreach ($freeItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if ($item->description)<div class="doc-item-desc">{{ $item->description }}</div>@endif
                            </td>
                            <td><span class="doc-strike">{{ money($item->grossValue(), $quotation->currency) }}</span></td>
                            <td class="doc-right"><strong class="doc-free">FREE</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="doc-note">Total value of complimentary services: <strong>{{ money($quotation->free_services_value, $quotation->currency) }}</strong></p>
        </section>
    @endif

    <section class="doc-section doc-totals-section">
        <table class="doc-totals">
            <tr><th>Subtotal</th><td>{{ money($quotation->subtotal, $quotation->currency) }}</td></tr>
            @if ((float) $quotation->discount_amount > 0)
                <tr><th>Discount</th><td>-{{ money($quotation->discount_amount, $quotation->currency) }}</td></tr>
            @endif
            @if ((float) $quotation->tax_amount > 0)
                <tr><th>{{ $settings->get('tax_name', 'Tax') }} ({{ rtrim(rtrim(number_format((float) $quotation->tax_percentage, 2), '0'), '.') }}%)</th>
                    <td>{{ money($quotation->tax_amount, $quotation->currency) }}</td></tr>
            @endif
            <tr class="doc-grand"><th>Grand Total</th><td>{{ money($quotation->grand_total, $quotation->currency) }}</td></tr>
        </table>
        @if ((float) $quotation->optional_items_value > 0)
            <p class="doc-note">Optional items are quoted separately and are not part of the grand total.</p>
        @endif
    </section>

    @if ($quotation->milestones->isNotEmpty())
        <section class="doc-section">
            <h2>Payment Schedule</h2>
            <table class="doc-table">
                <thead><tr><th style="width:34%">Milestone</th><th style="width:36%">Condition</th><th style="width:12%">%</th><th style="width:18%" class="doc-right">Amount</th></tr></thead>
                <tbody>
                    @foreach ($quotation->milestones as $milestone)
                        <tr>
                            <td><strong>{{ $milestone->title }}</strong>
                                @if ($milestone->description)<div class="doc-item-desc">{{ $milestone->description }}</div>@endif</td>
                            <td>{{ $milestone->due_condition ?: '—' }}</td>
                            <td>{{ $milestone->percentage !== null ? rtrim(rtrim(number_format((float) $milestone->percentage, 2), '0'), '.').'%' : '—' }}</td>
                            <td class="doc-right">{{ money($milestone->amount, $quotation->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    @if (is_array($quotation->technologies) && $quotation->technologies)
        <section class="doc-section">
            <h2>Technologies</h2>
            <p>{{ implode(' · ', $quotation->technologies) }}</p>
        </section>
    @endif

    @php
        $narrativeSections = [
            'Deliverables' => $quotation->deliverables,
            'Project Timeline' => $quotation->project_timeline,
            'Hosting & Domain' => $quotation->hosting_domain_info,
            'Third-Party Charges' => $quotation->third_party_charges,
            'Client Responsibilities' => $quotation->client_responsibilities,
        ];
    @endphp

    @foreach ($narrativeSections as $heading => $body)
        @if (filled($body))
            <section class="doc-section">
                <h2>{{ $heading }}</h2>
                <p>{{ $body }}</p>
            </section>
        @endif
    @endforeach

    @if ($quotation->revisions_included !== null || $quotation->support_period)
        <section class="doc-section">
            <h2>Revisions &amp; Support</h2>
            <p>
                @if ($quotation->revisions_included !== null){{ $quotation->revisions_included }} rounds of revisions are included.@endif
                @if ($quotation->support_period) Support period: {{ $quotation->support_period }}.@endif
            </p>
        </section>
    @endif

    @if (filled($quotation->terms_conditions))
        <section class="doc-section">
            <h2>Terms &amp; Conditions</h2>
            <p>{{ $quotation->terms_conditions }}</p>
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

    <section class="doc-section doc-acceptance">
        <h2>Client Acceptance</h2>
        <p>By signing below you accept this quotation and authorise us to begin the work described above.</p>
        <div class="doc-signatures">
            <div>
                <div class="doc-sign-line"></div>
                <div class="doc-sign-label">Client Name &amp; Signature</div>
                @if ($quotation->client_accepted_by)<div class="doc-sign-value">{{ $quotation->client_accepted_by }}</div>@endif
            </div>
            <div>
                <div class="doc-sign-line">
                    @if ($settings->get('agency_signature'))
                        <img src="{{ uploaded_url($settings->get('agency_signature')) }}" alt="" class="doc-sign-image">
                    @endif
                </div>
                <div class="doc-sign-label">For {{ $settings->get('agency_name', config('app.name')) }}</div>
            </div>
        </div>
    </section>

    <footer class="doc-footer">
        {{ $settings->get('agency_name', config('app.name')) }} &middot; {{ $quotation->quotation_number }}
        @if ($settings->get('agency_website')) &middot; {{ $settings->get('agency_website') }}@endif
    </footer>
</div>

</body>
</html>
