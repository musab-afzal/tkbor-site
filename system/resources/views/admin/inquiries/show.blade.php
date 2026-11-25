@extends('admin.layouts.admin')

@section('title', $inquiry->inquiry_number)
@section('page-title', 'Website Inquiry')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $inquiry->name }}
                <span class="admin-badge admin-badge--{{ $inquiry->status }}">
                    {{ \App\Models\WebsiteInquiry::STATUSES[$inquiry->status] ?? $inquiry->status }}
                </span>
            </h1>
            <p>{{ $inquiry->inquiry_number }} &middot; received {{ $inquiry->created_at?->format('M d, Y g:i A') }}</p>
        </div>
        <div class="admin-btn-group">
            @can('inquiries.edit')
                <a href="{{ route('admin.inquiries.edit', $inquiry) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.inquiries.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__body">
            <div class="admin-btn-group">
                @can('inquiries.convert')
                    @if ($inquiry->convertedLead)
                        <a href="{{ route('admin.leads.show', $inquiry->convertedLead) }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                            <i class="fas fa-bullseye"></i> Lead {{ $inquiry->convertedLead->lead_number }}
                        </a>
                    @else
                        <form action="{{ route('admin.inquiries.convert-lead', $inquiry) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Convert this inquiry into a lead?">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-bullseye"></i> Convert to Lead</button>
                        </form>
                    @endif

                    @if ($inquiry->convertedClient)
                        <a href="{{ route('admin.clients.show', $inquiry->convertedClient) }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                            <i class="fas fa-user-tie"></i> Client {{ $inquiry->convertedClient->displayName() }}
                        </a>
                    @else
                        <form action="{{ route('admin.inquiries.convert-client', $inquiry) }}" method="POST" class="admin-inline-form js-confirm-form" data-confirm="Convert this inquiry into a client? A lead is created first so the history is kept.">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-user-plus"></i> Convert to Client</button>
                        </form>
                    @endif
                @endcan

                @can('quotations.create')
                    <form action="{{ route('admin.inquiries.quotation', $inquiry) }}" method="POST" class="admin-inline-form">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-file-invoice"></i> Create Quotation</button>
                    </form>
                @endcan

                @can('follow_ups.create')
                    <a href="{{ route('admin.follow-ups.create') }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                        <i class="fas fa-phone-volume"></i> Add Follow-up
                    </a>
                @endcan

                @if ($inquiry->email)
                    <a href="mailto:{{ $inquiry->email }}?subject=Re: {{ $inquiry->subject ?: 'Your enquiry' }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                        <i class="fas fa-reply"></i> Reply by Email
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>{{ $inquiry->subject ?: 'Message' }}</h2></div>
                <div class="admin-card__body">
                    <div style="white-space:pre-line">{{ $inquiry->message ?: 'No message was submitted.' }}</div>
                </div>
            </div>

            @can('inquiries.edit')
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Assign</h2></div>
                    <div class="admin-card__body">
                        <form action="{{ route('admin.inquiries.assign', $inquiry) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-label" for="assigned_to">Assigned Employee</label>
                                    <select name="assigned_to" id="assigned_to" class="admin-select">
                                        <option value="">Unassigned</option>
                                        @foreach ($employees as $id => $name)
                                            <option value="{{ $id }}" @selected($inquiry->assigned_to == $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="admin-form-actions">
                                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-user-check"></i> Save Assignment</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            @if ($inquiry->followUps->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Follow Ups</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($inquiry->followUps as $followUp)
                            <div class="admin-mini-list__item">
                                <div>
                                    <div class="admin-mini-list__title">{{ \App\Models\FollowUp::TYPES[$followUp->type] ?? $followUp->type }}</div>
                                    <div class="admin-mini-list__meta">{{ $followUp->scheduled_at?->format('M d, Y g:i A') }}</div>
                                </div>
                                <span class="admin-badge admin-badge--{{ $followUp->status }}">{{ \App\Models\FollowUp::STATUSES[$followUp->status] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Sender</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Name</dt><dd>{{ $inquiry->name }}</dd></div>
                        <div><dt>Company</dt><dd>{{ $inquiry->company ?: '—' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $inquiry->email ?: '—' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $inquiry->phone ?: '—' }}</dd></div>
                        <div><dt>WhatsApp</dt><dd>{{ $inquiry->whatsapp ?: '—' }}</dd></div>
                        <div><dt>Service</dt><dd>{{ $inquiry->service ?: '—' }}</dd></div>
                        <div><dt>Budget</dt><dd>{{ $inquiry->budget ?: '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Submission</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Source Page</dt><dd>{{ $inquiry->source_page ?: '—' }}</dd></div>
                        <div><dt>Received</dt><dd>{{ $inquiry->created_at?->format('M d, Y g:i A') }}</dd></div>
                        <div><dt>Read</dt><dd>{{ $inquiry->read_at?->format('M d, Y g:i A') ?? 'Unread' }}</dd></div>
                        <div><dt>Priority</dt><dd><span class="admin-badge admin-badge--{{ $inquiry->priority }}">{{ \App\Models\WebsiteInquiry::PRIORITIES[$inquiry->priority] }}</span></dd></div>
                        <div><dt>Assigned To</dt><dd>{{ $inquiry->assignee?->name ?? 'Unassigned' }}</dd></div>
                        <div><dt>IP Address</dt><dd>{{ $inquiry->ip_address ?: '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            @if ($inquiry->admin_notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Internal Notes</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $inquiry->admin_notes }}</div></div>
                </div>
            @endif
        </div>
    </div>

@endsection
