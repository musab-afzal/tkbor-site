@extends('admin.layouts.admin')

@section('title', $task->title)
@section('page-title', 'Task '.$task->task_number)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>
                {{ $task->title }}
                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $task->status) }}">
                    {{ \App\Models\Task::STATUSES[$task->status] ?? $task->status }}
                </span>
                @if ($task->isOverdue())<span class="admin-badge admin-badge--overdue">Overdue</span>@endif
            </h1>
            <p>
                {{ $task->task_number }}
                @if ($task->project) &middot; <a href="{{ route('admin.projects.show', $task->project) }}">{{ $task->project->name }}</a>@endif
            </p>
        </div>
        <div class="admin-btn-group">
            @can('tasks.edit')
                @if ($task->status !== \App\Models\Task::STATUS_COMPLETED)
                    <form action="{{ route('admin.tasks.complete', $task) }}" method="POST" class="admin-inline-form">
                        @csrf @method('PATCH')
                        <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Mark Complete</button>
                    </form>
                @endif
                <a href="{{ route('admin.tasks.edit', $task) }}" class="admin-btn admin-btn--ghost"><i class="fas fa-pen"></i> Edit</a>
            @endcan
            <a href="{{ route('admin.tasks.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div>
            @if ($task->description)
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Description</h2></div>
                    <div class="admin-card__body"><div style="white-space:pre-line">{{ $task->description }}</div></div>
                </div>
            @endif

            <div class="admin-card">
                <div class="admin-card__header">
                    <h2>Checklist</h2>
                    @if ($task->checklistProgress() !== null)
                        <span class="admin-tab__count">{{ $task->checklistProgress() }}%</span>
                    @endif
                </div>
                <div class="admin-card__body">
                    <div class="admin-checklist">
                        @forelse ($task->checklistItems as $item)
                            <div class="admin-checklist__item {{ $item->is_done ? 'is-done' : '' }}">
                                @can('tasks.edit')
                                    <form action="{{ route('admin.tasks.checklist.toggle', [$task, $item->id]) }}" method="POST" class="admin-inline-form">
                                        @csrf @method('PATCH')
                                        <input type="checkbox" @checked($item->is_done) data-checklist-toggle>
                                    </form>
                                @else
                                    <input type="checkbox" @checked($item->is_done) disabled>
                                @endcan
                                <label>{{ $item->title }}</label>
                                @can('tasks.edit')
                                    <form action="{{ route('admin.tasks.checklist.destroy', [$task, $item->id]) }}" method="POST" class="admin-inline-form" style="margin-left:auto">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Remove"><i class="fas fa-xmark"></i></button>
                                    </form>
                                @endcan
                            </div>
                        @empty
                            <div class="admin-mini-list__empty">No checklist items yet.</div>
                        @endforelse
                    </div>

                    @can('tasks.edit')
                        <form action="{{ route('admin.tasks.checklist.store', $task) }}" method="POST" style="margin-top:14px">
                            @csrf
                            <div style="display:flex; gap:8px;">
                                <input type="text" name="title" class="admin-input" placeholder="Add a checklist item" required>
                                <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-plus"></i></button>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Comments</h2></div>
                <div class="admin-card__body">
                    <form action="{{ route('admin.tasks.comments.store', $task) }}" method="POST">
                        @csrf
                        <textarea name="body" rows="3" class="admin-textarea" placeholder="Leave a comment..." required></textarea>
                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-comment"></i> Post Comment</button>
                        </div>
                    </form>

                    <div class="admin-timeline" style="margin-top:18px">
                        @forelse ($task->comments as $comment)
                            <div class="admin-timeline__item">
                                <div class="admin-timeline__title">{{ $comment->admin?->name ?? 'Someone' }}</div>
                                <div class="admin-timeline__meta">{{ $comment->created_at?->diffForHumans() }}</div>
                                <div class="admin-timeline__body" style="white-space:pre-line">{{ $comment->body }}</div>
                            </div>
                        @empty
                            <div class="admin-mini-list__empty">No comments yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if ($task->timesheets->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Time Logged</h2></div>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead><tr><th>Date</th><th>Employee</th><th>Hours</th><th>Description</th></tr></thead>
                            <tbody>
                                @foreach ($task->timesheets as $timesheet)
                                    <tr>
                                        <td>{{ $timesheet->date?->format('M d, Y') }}</td>
                                        <td>{{ $timesheet->employee?->name }}</td>
                                        <td>{{ $timesheet->hours }}</td>
                                        <td class="admin-cell-muted">{{ Str::limit($timesheet->description, 60) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Details</h2></div>
                <div class="admin-card__body">
                    <dl class="admin-definition-list">
                        <div><dt>Priority</dt><dd><span class="admin-badge admin-badge--{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }}</span></dd></div>
                        <div><dt>Progress</dt><dd>{{ $task->progress }}%</dd></div>
                        <div><dt>Start Date</dt><dd>{{ $task->start_date?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt>Due Date</dt><dd>
                            {{ $task->due_date?->format('M d, Y') ?? '—' }}
                            @if ($task->due_time) {{ \Illuminate\Support\Carbon::parse($task->due_time)->format('g:i A') }}@endif
                        </dd></div>
                        <div><dt>Estimated Hours</dt><dd>{{ $task->estimated_hours ?? '—' }}</dd></div>
                        <div><dt>Actual Hours</dt><dd>{{ $task->actual_hours }}</dd></div>
                        <div><dt>Assigned By</dt><dd>{{ $task->assignedBy?->name ?? '—' }}</dd></div>
                        <div><dt>Client</dt><dd>{{ $task->client?->displayName() ?? '—' }}</dd></div>
                        <div><dt>Department</dt><dd>{{ $task->department?->name ?? '—' }}</dd></div>
                        <div><dt>Completed</dt><dd>{{ $task->completed_at?->format('M d, Y') ?? '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card__header"><h2>Assignees</h2></div>
                <div class="admin-mini-list">
                    @forelse ($task->assignees as $assignee)
                        <div class="admin-mini-list__item">
                            <div>
                                <div class="admin-mini-list__title">{{ $assignee->name }}</div>
                                <div class="admin-mini-list__meta">{{ $assignee->designation ?: '—' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-mini-list__empty">Unassigned.</div>
                    @endforelse
                </div>
            </div>

            @if ($task->tags->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Tags</h2></div>
                    <div class="admin-card__body">
                        @foreach ($task->tags as $tag)
                            <span class="admin-chip">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($task->dependencies->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Depends On</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($task->dependencies as $dependency)
                            <a href="{{ route('admin.tasks.show', $dependency) }}" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $dependency->title }}</div>
                                    <div class="admin-mini-list__meta">{{ $dependency->task_number }}</div></div>
                                <span class="admin-badge admin-badge--{{ str_replace('_', '-', $dependency->status) }}">
                                    {{ \App\Models\Task::STATUSES[$dependency->status] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($task->attachments->isNotEmpty())
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Attachments</h2></div>
                    <div class="admin-mini-list">
                        @foreach ($task->attachments as $attachment)
                            <a href="{{ uploaded_url($attachment->path) }}" target="_blank" rel="noopener" class="admin-mini-list__item">
                                <div><div class="admin-mini-list__title">{{ $attachment->original_name }}</div>
                                    <div class="admin-mini-list__meta">{{ $attachment->humanSize() }}</div></div>
                                <i class="fas fa-download"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
