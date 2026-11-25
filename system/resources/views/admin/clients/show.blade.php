@extends('admin.layouts.admin')

@section('title', $client->displayName())
@section('page-title', 'Client')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $client->displayName() }}
                <span class="admin-badge {{ $client->status ? 'admin-badge--active' : 'admin-badge--inactive' }}">
                    {{ $client->status ? 'Active' : 'Inactive' }}
                </span>
            </h1>
            <p>{{ $client->client_code }} &middot; {{ \App\Models\Client::TYPES[$client->type] ?? $client->type }}</p>
        </div>
        <div class="admin-btn-group">
            @can('quotations.create')
                <a href="{{ route('admin.quotations.create', ['client_id' => $client->id]) }}" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-file-invoice"></i> New Quotation
                </a>
            @endcan
            @can('invoices.create')
                <a href="{{ route('admin.invoices.create', ['client_id' => $client->id]) }}" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-file-invoice-dollar"></i> New Invoice
                </a>
            @endcan
            @can('clients.edit')
                <a href="{{ route('admin.clients.edit', $client) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.clients.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Total Invoiced</div><div class="admin-kpi__value">{{ money($totals['invoiced']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--info"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Received</div><div class="admin-kpi__value">{{ money($totals['received']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--success"><i class="fas fa-money-bill-transfer"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Outstanding</div><div class="admin-kpi__value">{{ money($totals['outstanding']) }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--danger"><i class="fas fa-hand-holding-dollar"></i></div>
        </div>
        <div class="admin-kpi">
            <div><div class="admin-kpi__label">Active Projects</div><div class="admin-kpi__value">{{ $totals['projects_active'] }}</div></div>
            <div class="admin-kpi__icon admin-kpi__icon--primary"><i class="fas fa-diagram-project"></i></div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            @php
                $relatedPanels = [
                    ['title' => 'Projects', 'items' => $client->projects, 'route' => 'admin.projects.show', 'label' => 'name', 'meta' => 'project_number', 'status' => 'status', 'statuses' => \App\Models\Project::STATUSES],
                    ['title' => 'Quotations', 'items' => $client->quotations, 'route' => 'admin.quotations.show', 'label' => 'project_name', 'meta' => 'quotation_number', 'status' => 'status', 'statuses' => \App\Models\Quotation::STATUSES],
                    ['title' => 'Invoices', 'items' => $client->invoices, 'route' => 'admin.invoices.show', 'label' => 'invoice_number', 'meta' => null, 'status' => 'status', 'statuses' => \App\Models\Invoice::STATUSES],
                    ['title' => 'Support Tickets', 'items' => $client->tickets, 'route' => 'admin.tickets.show', 'label' => 'subject', 'meta' => 'ticket_number', 'status' => 'status', 'statuses' => \App\Models\Ticket::STATUSES],
                ];
            @endphp

            @foreach ($relatedPanels as $panel)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>{{ $panel['title'] }}</h2></div>
                    <div class="admin-mini-list">
                        @forelse ($panel['items'] as $item)
                            <a href="{{ route($panel['route'], $item) }}" class="admin-mini-list__item">
                                <div>
                                    <div class="admin-mini-list__title">{{ $item->{$panel['label']} }}</div>
                                    <div class="admin-mini-list__meta">
                                        @if ($panel['meta']){{ $item->{$panel['meta']} }} &middot; @endif
                                        {{ $item->created_at?->format('M d, Y') }}
                                    </div>
                                </div>
                                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $item->{$panel['status']}) }}">
                                    {{ $panel['statuses'][$item->{$panel['status']}] ?? $item->{$panel['status']} }}
                                </span>
                            </a>
                        @empty
                            <div class="admin-mini-list__empty">Nothing yet.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <div class="admin-card">
                <div class="admin-card__header"><h2>Payments</h2></div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Payment #</th><th>Invoice</th><th>Date</th><th>Method</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse ($client->payments as $payment)
                                <tr>
                                    <td><span class="admin-cell-code">{{ $payment->payment_number }}</span></td>
                                    <td>{{ $payment->invoice?->invoice_number ?? '—' }}</td>
                                    <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                                    <td>{{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</td>
                                    <td>{{ money($payment->amount, $payment->currency) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-state">No payments recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Meetings & Follow-ups</h2></div>
                <div class="admin-mini-list">
                    @forelse ($client->meetings as $meeting)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $meeting->title }}</div>
                                <div class="admin-mini-list__meta">{{ $meeting->scheduled_at?->format('M d, Y g:i A') }}</div>
                            </div>
                            <span class="admin-badge admin-badge--{{ str_replace('_', '-', $meeting->status) }}">
                                {{ \App\Models\Meeting::STATUSES[$meeting->status] ?? $meeting->status }}
                            </span>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">No meetings logged.</div>
                    @endforelse

                    @foreach ($client->followUps as $followUp)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">
                                    {{ \App\Models\FollowUp::TYPES[$followUp->type] ?? $followUp->type }} follow-up
                                </div>
                                <div class="admin-mini-list__meta">
                                    {{ $followUp->scheduled_at?->format('M d, Y g:i A') }}
                                    @if ($followUp->assignee) &middot; {{ $followUp->assignee->name }}@endif
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--{{ $followUp->isOverdue() ? 'overdue' : $followUp->status }}">
                                {{ $followUp->isOverdue() ? 'Overdue' : (\App\Models\FollowUp::STATUSES[$followUp->status] ?? $followUp->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Details</h2></div>
                <div class="admin-card__body">
                    @if ($client->logo)
                        <img src="{{ uploaded_url($client->logo) }}" alt="" style="max-width:140px; margin-bottom:14px;">
                    @endif
                    <dl class="admin-definition-list">
                        <div><dt>Contact Person</dt><dd>{{ $client->contact_person ?: $client->name }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $client->email ?: '—' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $client->phone ?: '—' }}</dd></div>
                        <div><dt>WhatsApp</dt><dd>{{ $client->whatsapp ?: '—' }}</dd></div>
                        <div><dt>Website</dt><dd>{{ $client->website ?: '—' }}</dd></div>
                        <div><dt>Industry</dt><dd>{{ $client->industry ?: '—' }}</dd></div>
                        <div><dt>Address</dt><dd>{{ $client->address ?: '—' }}</dd></div>
                        <div><dt>City / Country</dt><dd>{{ collect([$client->city, $client->country])->filter()->join(', ') ?: '—' }}</dd></div>
                        <div><dt>{{ $client->tax_name ?: 'Tax Number' }}</dt><dd>{{ $client->tax_number ?: '—' }}</dd></div>
                        <div><dt>Currency</dt><dd>{{ $client->currency ?: settings()->currency() }}</dd></div>
                        @if ($client->lead)
                            <div><dt>Originating Lead</dt><dd><a href="{{ route('admin.leads.show', $client->lead) }}">{{ $client->lead->lead_number }}</a></dd></div>
                        @endif
                        <div><dt>Client Since</dt><dd>{{ $client->created_at?->format('M d, Y') }}</dd></div>
                    </dl>
                </div>
            </div>

            @if ($client->contracts->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Contracts</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($client->contracts as $contract)
                            <div class="admin-mini-list__item">
                                <div>
                                    <div class="admin-mini-list__title">{{ $contract->title }}</div>
                                    <div class="admin-mini-list__meta">{{ $contract->contract_number }} &middot; {{ money($contract->value, $contract->currency) }}</div>
                                </div>
                                <span class="admin-badge admin-badge--{{ $contract->status }}">{{ \App\Models\Contract::STATUSES[$contract->status] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($client->assets->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Domains & Hosting</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($client->assets as $asset)
                            <div class="admin-mini-list__item">
                                <div>
                                    <div class="admin-mini-list__title">{{ $asset->name }}</div>
                                    <div class="admin-mini-list__meta">
                                        {{ \App\Models\Asset::TYPES[$asset->type] }}
                                        @if ($asset->expiry_date) &middot; expires {{ $asset->expiry_date->format('M d, Y') }}@endif
                                    </div>
                                </div>
                                @if ($asset->isExpired())
                                    <span class="admin-badge admin-badge--expired">Expired</span>
                                @elseif ($asset->isExpiringSoon())
                                    <span class="admin-badge admin-badge--warning">Soon</span>
                                @else
                                    <span class="admin-badge admin-badge--active">Active</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($client->notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Internal Notes</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $client->notes }}</div></div>
                </div>
            @endif
        </div>
    </div>

@endsection
