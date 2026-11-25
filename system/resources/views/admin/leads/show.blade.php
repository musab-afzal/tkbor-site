@extends('admin.layouts.admin')

@section('title', $lead->lead_number)
@section('page-title', 'Lead '.$lead->lead_number)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $lead->name }}
                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $lead->status) }}">
                    {{ \App\Models\Lead::STATUSES[$lead->status] ?? $lead->status }}
                </span>
            </h1>
            <p>{{ $lead->lead_number }} @if ($lead->company) &middot; {{ $lead->company }} @endif</p>
        </div>
        <div class="admin-btn-group">
            @can('leads.edit')
                <a href="{{ route('admin.leads.edit', $lead) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.leads.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__body">
            <div class="admin-btn-group">
                @can('leads.convert')
                    @if ($lead->isConverted())
                        <a href="{{ route('admin.clients.show', $lead->client) }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                            <i class="fas fa-user-tie"></i> View Client {{ $lead->client->displayName() }}
                        </a>
                    @else
                        <form action="{{ route('admin.leads.convert-client', $lead) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Convert this lead into a client?">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-user-plus"></i> Convert to Client</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.leads.convert-project', $lead) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Create a project from this lead?">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-diagram-project"></i> Convert to Project</button>
                    </form>
                @endcan

                @can('quotations.create')
                    @if ($lead->client_id)
                        <a href="{{ route('admin.quotations.create', ['client_id' => $lead->client_id]) }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                            <i class="fas fa-file-invoice"></i> New Quotation
                        </a>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Add Activity</h2></div>
                <div class="admin-card__body">
                    <form action="{{ route('admin.leads.activities.store', $lead) }}" method="POST">
                        @csrf
                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <label class="admin-label" for="type">Type</label>
                                <select name="type" id="type" class="admin-select" required>
                                    @foreach (\App\Models\LeadActivity::TYPES as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-label" for="title">Title</label>
                                <input type="text" name="title" id="title" class="admin-input" required placeholder="Called the client">
                            </div>
                            <div class="admin-form-group admin-form-group--full">
                                <label class="admin-label" for="description">Notes</label>
                                <textarea name="description" id="description" rows="2" class="admin-textarea"></textarea>
                            </div>
                        </div>
                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-plus"></i> Add Activity</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Activity History</h2></div>
                <div class="admin-card__body">
                    <div class="admin-timeline">
                        @forelse ($lead->activities as $activity)
                            <div class="admin-timeline__item">
                                <div class="admin-timeline__title">{{ $activity->title }}</div>
                                <div class="admin-timeline__meta">
                                    {{ \App\Models\LeadActivity::TYPES[$activity->type] ?? $activity->type }}
                                    &middot; {{ $activity->admin?->name ?? 'System' }}
                                    &middot; {{ $activity->created_at?->diffForHumans() }}
                                </div>
                                @if ($activity->description)
                                    <div class="admin-timeline__body">{{ $activity->description }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="admin-mini-list__empty">No activity recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if ($lead->quotations->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Quotations</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($lead->quotations as $quotation)
                            <a href="{{ route('admin.quotations.show', $quotation) }}" class="admin-mini-list__item">
                                <div>
                                    <div class="admin-mini-list__title">{{ $quotation->quotation_number }}</div>
                                    <div class="admin-mini-list__meta">{{ $quotation->project_name }} &middot; {{ money($quotation->grand_total, $quotation->currency) }}</div>
                                </div>
                                <span class="admin-badge admin-badge--{{ $quotation->status }}">{{ \App\Models\Quotation::STATUSES[$quotation->status] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($lead->followUps->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Follow Ups</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($lead->followUps as $followUp)
                            <div class="admin-mini-list__item">
                                <div>
                                    <div class="admin-mini-list__title">{{ \App\Models\FollowUp::TYPES[$followUp->type] ?? $followUp->type }}</div>
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
            @endif
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Lead Details</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Email</dt><dd>{{ $lead->email ?: '—' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $lead->phone ?: '—' }}</dd></div>
                        <div><dt>WhatsApp</dt><dd>{{ $lead->whatsapp ?: '—' }}</dd></div>
                        <div><dt>Source</dt><dd>{{ \App\Models\Lead::SOURCES[$lead->source] ?? '—' }}</dd></div>
                        <div><dt>Interested Service</dt><dd>{{ $lead->interested_service ?: $lead->service?->title ?: '—' }}</dd></div>
                        <div><dt>Estimated Budget</dt><dd>{{ $lead->estimated_budget ? money($lead->estimated_budget, $lead->currency) : '—' }}</dd></div>
                        <div><dt>Priority</dt><dd><span class="admin-badge admin-badge--{{ $lead->priority }}">{{ \App\Models\Lead::PRIORITIES[$lead->priority] }}</span></dd></div>
                        <div><dt>Assigned To</dt><dd>{{ $lead->assignee?->name ?? 'Unassigned' }}</dd></div>
                        <div><dt>Next Follow-up</dt><dd>{{ $lead->next_follow_up_at?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Location</dt><dd>{{ collect([$lead->city, $lead->country])->filter()->join(', ') ?: '—' }}</dd></div>
                        <div><dt>Created</dt><dd>{{ $lead->created_at?->format('M d, Y') }}</dd></div>
                        @if ($lead->websiteInquiry)
                            <div><dt>From Inquiry</dt><dd><a href="{{ route('admin.inquiries.show', $lead->websiteInquiry) }}">{{ $lead->websiteInquiry->inquiry_number }}</a></dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($lead->notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Notes</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $lead->notes }}</div></div>
                </div>
            @endif

            @if ($lead->lost_reason)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Lost Reason</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $lead->lost_reason }}</div></div>
                </div>
            @endif
        </div>
    </div>

@endsection
