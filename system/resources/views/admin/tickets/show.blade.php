@extends('admin.layouts.admin')

@section('title', $ticket->ticket_number)
@section('page-title', 'Ticket '.$ticket->ticket_number)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $ticket->subject }}
                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $ticket->status) }}">
                    {{ \App\Models\Ticket::STATUSES[$ticket->status] ?? $ticket->status }}
                </span>
            </h1>
            <p>
                {{ $ticket->ticket_number }}
                @if ($ticket->client) &middot; <a href="{{ route('admin.clients.show', $ticket->client) }}">{{ $ticket->client->displayName() }}</a>@endif
            </p>
        </div>
        <div class="admin-btn-group">
            @can('tickets.edit')
                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="admin-btn admin-btn--primary"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Description</h2></div>
                <div class="admin-card__body">
                    <div style="white-space:pre-line">{{ $ticket->description ?: 'No description provided.' }}</div>

                    @if ($ticket->attachments->isNotEmpty())
                        <div style="margin-top:14px">
                            @foreach ($ticket->attachments as $attachment)
                                <a href="{{ uploaded_url($attachment->path) }}" target="_blank" rel="noopener" class="admin-chip">
                                    <i class="fas fa-paperclip"></i> {{ $attachment->original_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Conversation</h2></div>
                <div class="admin-card__body">
                    <div class="admin-timeline">
                        @forelse ($ticket->replies as $reply)
                            <div class="admin-timeline__item">
                                <div class="admin-timeline__title">
                                    {{ $reply->author_name ?? $reply->admin?->name ?? 'Support' }}
                                    @if ($reply->is_internal)<span class="admin-badge admin-badge--warning">Internal note</span>@endif
                                </div>
                                <div class="admin-timeline__meta">{{ $reply->created_at?->format('M d, Y g:i A') }}</div>
                                <div class="admin-timeline__body" style="white-space:pre-line">{{ $reply->body }}</div>
                            </div>
                        @empty
                            <div class="admin-mini-list__empty">No replies yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @can('tickets.reply')
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Add Reply</h2></div>
                    <div class="admin-card__body">
                        @include('admin.partials.validation-summary')

                        <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST">
                            @csrf
                            <div class="admin-form-grid">
                                <div class="admin-form-group admin-form-group--full">
                                    <label class="admin-label" for="body">Message</label>
                                    <textarea id="body" name="body" rows="5" class="admin-textarea" required></textarea>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label" for="status">Set Status</label>
                                    <select id="status" name="status" class="admin-select">
                                        <option value="">Leave unchanged</option>
                                        @foreach (\App\Models\Ticket::STATUSES as $value => $label)
                                            <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label">Visibility</label>
                                    <div class="admin-checkbox-row">
                                        <input type="checkbox" id="is_internal" name="is_internal" value="1">
                                        <label for="is_internal" class="admin-checkbox-label">Internal note (not for the client)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="admin-form-actions">
                                <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-reply"></i> Post Reply</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Details</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Category</dt><dd>{{ \App\Models\Ticket::CATEGORIES[$ticket->category] ?? $ticket->category }}</dd></div>
                        <div><dt>Priority</dt><dd><span class="admin-badge admin-badge--{{ $ticket->priority }}">{{ \App\Models\Ticket::PRIORITIES[$ticket->priority] }}</span></dd></div>
                        <div><dt>Project</dt><dd>
                            @if ($ticket->project)
                                <a href="{{ route('admin.projects.show', $ticket->project) }}">{{ $ticket->project->name }}</a>
                            @else — @endif
                        </dd></div>
                        <div><dt>Contact</dt><dd>{{ $ticket->contact_name ?: '—' }}</dd></div>
                        <div><dt>Contact Email</dt><dd>{{ $ticket->contact_email ?: '—' }}</dd></div>
                        <div><dt>Opened</dt><dd>{{ $ticket->created_at?->format('M d, Y g:i A') }}</dd></div>
                        <div><dt>First Response</dt><dd>{{ $ticket->first_response_at?->format('M d, Y g:i A') ?? 'Not yet' }}</dd></div>
                        <div><dt>Resolved</dt><dd>{{ $ticket->resolved_at?->format('M d, Y g:i A') ?? '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            @can('tickets.edit')
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Assign</h2></div>
                    <div class="admin-card__body">
                        <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="admin-form-group">
                                <label class="admin-label" for="assigned_to">Assigned Employee</label>
                                <select id="assigned_to" name="assigned_to" class="admin-select">
                                    <option value="">Unassigned</option>
                                    @foreach ($employees as $id => $name)
                                        <option value="{{ $id }}" @selected($ticket->assigned_to == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="admin-form-actions">
                                <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-user-check"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            @if ($ticket->resolution)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Resolution</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $ticket->resolution }}</div></div>
                </div>
            @endif
        </div>
    </div>

@endsection
