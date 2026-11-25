@extends('admin.layouts.admin')

@section('title', $todo->title)
@section('page-title', 'TODO')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $todo->title }}
                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $todo->status) }}">
                    {{ \App\Models\Todo::STATUSES[$todo->status] ?? $todo->status }}
                </span>
                @if ($todo->isOverdue())<span class="admin-badge admin-badge--overdue">Overdue</span>@endif
            </h1>
            <p>
                Created by {{ $todo->creator?->name ?? 'Someone' }}
                @if ($todo->assignee) &middot; assigned to {{ $todo->assignee->name }} @endif
            </p>
        </div>
        <div class="admin-btn-group">
            @can('todos.edit')
                @if ($todo->status !== \App\Models\Todo::STATUS_COMPLETED)
                    <form action="{{ route('admin.todos.complete', $todo) }}" method="POST" class="admin-inline-form">
                        @csrf @method('PATCH')
                        <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Mark Complete</button>
                    </form>
                @endif
                <a href="{{ route('admin.todos.edit', $todo) }}" class="admin-btn admin-btn--ghost"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.todos.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            @if ($todo->description)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Description</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $todo->description }}</div></div>
                </div>
            @endif

            <div class="admin-card">
                <div class="admin-card__header"><h2>Checklist</h2></div>
                <div class="admin-card__body">
                    <div class="admin-checklist">
                        @forelse ($todo->items as $item)
                            <div class="admin-checklist__item {{ $item->is_done ? 'is-done' : '' }}">
                                @can('todos.edit')
                                    <form action="{{ route('admin.todos.items.toggle', [$todo, $item->id]) }}" method="POST" class="admin-inline-form">
                                        @csrf @method('PATCH')
                                        <input type="checkbox" @checked($item->is_done) data-checklist-toggle>
                                    </form>
                                @else
                                    <input type="checkbox" @checked($item->is_done) disabled>
                                @endcan
                                <label>{{ $item->title }}</label>
                            </div>
                        @empty
                            <div class="admin-mini-list__empty">No checklist items. Add them by editing this TODO.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if ($todo->notes)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Notes</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $todo->notes }}</div></div>
                </div>
            @endif
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Details</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Priority</dt><dd><span class="admin-badge admin-badge--{{ $todo->priority }}">{{ \App\Models\Todo::PRIORITIES[$todo->priority] }}</span></dd></div>
                        <div><dt>Date</dt><dd>{{ $todo->date?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Due Date</dt><dd>{{ $todo->due_date?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Visibility</dt><dd>{{ $todo->is_company_wide ? 'Company wide' : 'Private' }}</dd></div>
                        <div><dt>Completed</dt><dd>{{ $todo->completed_at?->format('M d, Y') ?? '—' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

@endsection
