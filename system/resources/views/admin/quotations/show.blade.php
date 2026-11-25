@extends('admin.layouts.admin')

@section('title', $quotation->quotation_number)
@section('page-title', 'Quotation '.$quotation->quotation_number)

@section('content')

    @php $canSeeCost = auth('admin')->user()?->hasPermission('quotations.view_cost'); @endphp

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $quotation->quotation_number }}
                <span class="admin-badge admin-badge--{{ $quotation->status }}">
                    {{ \App\Models\Quotation::STATUSES[$quotation->status] ?? $quotation->status }}
                </span>
            </h1>
            <p>{{ $quotation->project_name }} &middot; {{ $quotation->client?->displayName() }}</p>
        </div>
        <div class="admin-btn-group">
            <a href="{{ route('admin.quotations.print', $quotation) }}" target="_blank" rel="noopener" class="admin-btn admin-btn--ghost">
                <i class="fas fa-print"></i> Print / PDF
            </a>
            @can('quotations.edit')
                @if ($quotation->isEditable())
                    <a href="{{ route('admin.quotations.edit', $quotation) }}" class="admin-btn admin-btn--primary">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                @endif
            @endcan
            <a href="{{ route('admin.quotations.index') }}" class="admin-btn admin-btn--ghost">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @isset($lockedNotice)
        <div class="admin-alert-inline admin-alert-inline--warning">
            <i class="fas fa-lock"></i> <span>{{ $lockedNotice }}</span>
        </div>
    @endisset

    <div class="admin-card">
        <div class="admin-card__body">
            <div class="admin-btn-group">
                @can('quotations.edit')
                    @if ($quotation->status === \App\Models\Quotation::STATUS_DRAFT)
                        <form action="{{ route('admin.quotations.send', $quotation) }}" method="POST" class="admin-inline-form">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-paper-plane"></i> Mark as Sent</button>
                        </form>
                    @endif
                @endcan

                @can('quotations.approve')
                    @if (in_array($quotation->status, ['draft', 'sent'], true))
                        <form action="{{ route('admin.quotations.approve', $quotation) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Mark this quotation as approved by the client?">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-check"></i> Approve</button>
                        </form>
                        <form action="{{ route('admin.quotations.reject', $quotation) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Mark this quotation as rejected?">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm"><i class="fas fa-xmark"></i> Reject</button>
                        </form>
                    @endif
                @endcan

                @can('quotations.create')
                    <form action="{{ route('admin.quotations.duplicate', $quotation) }}" method="POST" class="admin-inline-form">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-copy"></i> Duplicate</button>
                    </form>
                    <form action="{{ route('admin.quotations.revise', $quotation) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Create a new revision of this quotation?">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-code-branch"></i> Create Revision</button>
                    </form>
                @endcan

                @can('projects.create')
                    @if ($quotation->status === \App\Models\Quotation::STATUS_APPROVED && ! $quotation->converted_project_id)
                        <form action="{{ route('admin.quotations.convert', $quotation) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Create a project from this quotation?">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-diagram-project"></i> Convert to Project</button>
                        </form>
                    @endif
                @endcan

                @if ($quotation->converted_project_id)
                    <a href="{{ route('admin.projects.show', $quotation->converted_project_id) }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                        <i class="fas fa-diagram-project"></i> {{ $quotation->convertedProject?->project_number }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Items</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Days</th>
                                @if ($canSeeCost)<th>Cost</th>@endif
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\Quotation::SECTIONS as $section => $sectionLabel)
                                @php $sectionItems = $quotation->itemsInSection($section); @endphp
                                @continue($sectionItems->isEmpty())
                                <tr><td colspan="{{ $canSeeCost ? 6 : 5 }}"><strong>{{ $sectionLabel }}</strong></td></tr>
                                @foreach ($sectionItems as $item)
                                    <tr>
                                        <td>
                                            <div class="admin-cell-stack">
                                                <span>
                                                    {{ $item->name }}
                                                    @if ($item->is_free)<span class="admin-badge admin-badge--success">Free</span>@endif
                                                    @if ($item->is_optional)<span class="admin-badge admin-badge--neutral">Optional</span>@endif
                                                </span>
                                                @if ($item->description)<small>{{ $item->description }}</small>@endif
                                            </div>
                                        </td>
                                        <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                                        <td>{{ money($item->unit_price, $quotation->currency) }}</td>
                                        <td>{{ $item->estimated_days ?: '—' }}</td>
                                        @if ($canSeeCost)<td>{{ $item->internal_cost !== null ? money($item->internal_cost, $quotation->currency) : '—' }}</td>@endif
                                        <td>{{ money($item->line_total, $quotation->currency) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="admin-card__body">
                    <div class="admin-totals">
                        <div class="admin-totals__row"><span>Subtotal</span><span>{{ money($quotation->subtotal, $quotation->currency) }}</span></div>
                        <div class="admin-totals__row"><span>Discount</span><span>-{{ money($quotation->discount_amount, $quotation->currency) }}</span></div>
                        <div class="admin-totals__row"><span>Tax ({{ rtrim(rtrim(number_format((float) $quotation->tax_percentage, 2), '0'), '.') }}%)</span><span>{{ money($quotation->tax_amount, $quotation->currency) }}</span></div>
                        @if ((float) $quotation->free_services_value > 0)
                            <div class="admin-totals__row"><span>Free services value</span><span>{{ money($quotation->free_services_value, $quotation->currency) }}</span></div>
                        @endif
                        @if ((float) $quotation->optional_items_value > 0)
                            <div class="admin-totals__row"><span>Optional items</span><span>{{ money($quotation->optional_items_value, $quotation->currency) }}</span></div>
                        @endif
                        <div class="admin-totals__row admin-totals__row--grand"><span>Grand Total</span><span>{{ money($quotation->grand_total, $quotation->currency) }}</span></div>
                        @if ($canSeeCost)
                            <div class="admin-totals__row"><span>Internal cost</span><span>{{ money($quotation->internal_cost_total, $quotation->currency) }}</span></div>
                            <div class="admin-totals__row"><span>Estimated margin</span><span>{{ money((float) $quotation->grand_total - (float) $quotation->internal_cost_total, $quotation->currency) }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($quotation->milestones->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Payment Milestones</h2></div>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead><tr><th>Milestone</th><th>Condition</th><th>%</th><th>Amount</th></tr></thead>
                            <tbody>
                                @foreach ($quotation->milestones as $milestone)
                                    <tr>
                                        <td>
                                            <div class="admin-cell-stack">
                                                <span>{{ $milestone->title }}</span>
                                                @if ($milestone->description)<small>{{ $milestone->description }}</small>@endif
                                            </div>
                                        </td>
                                        <td>{{ $milestone->due_condition ?: '—' }}</td>
                                        <td>{{ $milestone->percentage !== null ? rtrim(rtrim(number_format((float) $milestone->percentage, 2), '0'), '.').'%' : '—' }}</td>
                                        <td>{{ money($milestone->amount, $quotation->currency) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @php
                $narrativeSections = [
                    'Project Summary' => $quotation->project_summary,
                    'Requirements' => $quotation->requirements,
                    'Scope of Work' => $quotation->scope,
                    'Proposed Solution' => $quotation->proposed_solution,
                    'Deliverables' => $quotation->deliverables,
                    'Project Timeline' => $quotation->project_timeline,
                    'Hosting & Domain' => $quotation->hosting_domain_info,
                    'Third-Party Charges' => $quotation->third_party_charges,
                    'Client Responsibilities' => $quotation->client_responsibilities,
                    'Terms & Conditions' => $quotation->terms_conditions,
                ];
            @endphp

            @foreach ($narrativeSections as $heading => $body)
                @if (filled($body))
                    <div class="admin-card">
                        <div class="admin-card__header"><h2>{{ $heading }}</h2></div>
                        <div class="admin-card__body"><div style="white-space:pre-line">{{ $body }}</div></div>
                    </div>
                @endif
            @endforeach

            @if (filled($quotation->internal_notes))
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Internal Notes</h2></div>
                    <div class="admin-card__body">
                        <div class="admin-alert-inline admin-alert-inline--warning">
                            <i class="fas fa-eye-slash"></i> <span>Not shown to the client.</span>
                        </div>
                        <div style="white-space:pre-line">{{ $quotation->internal_notes }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Summary</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Client</dt><dd>
                            @if ($quotation->client)
                                <a href="{{ route('admin.clients.show', $quotation->client) }}">{{ $quotation->client->displayName() }}</a>
                            @else — @endif
                        </dd></div>
                        <div><dt>Project Type</dt><dd>{{ $quotation->project_type ?: '—' }}</dd></div>
                        <div><dt>Date</dt><dd>{{ $quotation->quotation_date?->format('M d, Y') }}</dd></div>
                        <div><dt>Valid Until</dt><dd>{{ $quotation->valid_until?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Prepared By</dt><dd>{{ $quotation->preparedBy?->name ?? '—' }}</dd></div>
                        <div><dt>Currency</dt><dd>{{ $quotation->currency }}</dd></div>
                        <div><dt>Estimated Duration</dt><dd>{{ $quotation->total_estimated_days }} days</dd></div>
                        <div><dt>Revisions Included</dt><dd>{{ $quotation->revisions_included ?? '—' }}</dd></div>
                        <div><dt>Support Period</dt><dd>{{ $quotation->support_period ?: '—' }}</dd></div>
                        @if ($quotation->lead)
                            <div><dt>From Lead</dt><dd><a href="{{ route('admin.leads.show', $quotation->lead) }}">{{ $quotation->lead->lead_number }}</a></dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($quotation->status === \App\Models\Quotation::STATUS_APPROVED)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Client Acceptance</h2></div>
                    <div class="admin-card__body">
                        <dl class="admin-definition-list">
                            <div><dt>Accepted By</dt><dd>{{ $quotation->client_accepted_by ?: '—' }}</dd></div>
                            <div><dt>Accepted On</dt><dd>{{ $quotation->client_accepted_date?->format('M d, Y') ?? '—' }}</dd></div>
                        </dl>
                        @if ($quotation->client_remarks)
                            <p style="margin-top:10px; white-space:pre-line">{{ $quotation->client_remarks }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if ($quotation->parent || $quotation->revisions->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Revisions</h2></div>
                    <div class="admin-mini-list">
                        @if ($quotation->parent)
                            <a href="{{ route('admin.quotations.show', $quotation->parent) }}" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $quotation->parent->quotation_number }}</div>
                                    <div class="admin-mini-list__meta">Original</div></div>
                                <span class="admin-badge admin-badge--{{ $quotation->parent->status }}">{{ \App\Models\Quotation::STATUSES[$quotation->parent->status] }}</span>
                            </a>
                        @endif
                        @foreach ($quotation->revisions as $revision)
                            <a href="{{ route('admin.quotations.show', $revision) }}" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $revision->quotation_number }}</div>
                                    <div class="admin-mini-list__meta">{{ money($revision->grand_total, $revision->currency) }}</div></div>
                                <span class="admin-badge admin-badge--{{ $revision->status }}">{{ \App\Models\Quotation::STATUSES[$revision->status] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($quotation->invoices->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Invoices</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($quotation->invoices as $invoice)
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $invoice->invoice_number }}</div>
                                    <div class="admin-mini-list__meta">{{ money($invoice->total, $invoice->currency) }}</div></div>
                                <span class="admin-badge admin-badge--{{ $invoice->status }}">{{ \App\Models\Invoice::STATUSES[$invoice->status] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
