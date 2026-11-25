@extends('admin.layouts.admin')

@section('title', 'Task Board')
@section('page-title', 'Task Board')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Task Board</h1>
            <p>Every task grouped by stage. Move a task on with the buttons on its card.</p>
        </div>
        <div class="admin-btn-group">
            <a href="{{ route('admin.tasks.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-list"></i> List View</a>
            @can('tasks.create')
                <a href="{{ route('admin.tasks.create') }}" class="admin-btn admin-btn--primary"><i class="fas fa-plus"></i> Add Task</a>
            @endcan
        </div>
    </div>

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.tasks.board') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field">
                <label class="admin-label" for="project_id">Project</label>
                <select name="project_id" id="project_id" class="admin-select">
                    <option value="">All projects</option>
                    @foreach ($projects as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('project_id') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="employee_id">Assignee</label>
                <select name="employee_id" id="employee_id" class="admin-select">
                    <option value="">Everyone</option>
                    @foreach ($employees as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('employee_id') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.tasks.board') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-kanban">
        @foreach (\App\Models\Task::STATUSES as $status => $label)
            @php $columnTasks = $grouped[$status] ?? collect(); @endphp
            <div class="admin-kanban__column">
                <div class="admin-kanban__header">
                    <span>{{ $label }}</span>
                    <span class="admin-tab__count">{{ $columnTasks->count() }}</span>
                </div>

                @forelse ($columnTasks as $task)
                    <div class="admin-kanban__card">
                        <div class="admin-kanban__card-title">
                            <a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a>
                        </div>
                        <div class="admin-kanban__card-meta">
                            <span class="admin-badge admin-badge--{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }}</span>
                            @if ($task->due_date)
                                @if ($task->isOverdue())
                                    <span class="admin-badge admin-badge--overdue">{{ $task->due_date->format('M d') }}</span>
                                @else
                                    <span><i class="fas fa-calendar"></i> {{ $task->due_date->format('M d') }}</span>
                                @endif
                            @endif
                        </div>
                        @if ($task->project)
                            <div class="admin-kanban__card-meta" style="margin-top:5px">
                                <i class="fas fa-diagram-project"></i> {{ Str::limit($task->project->name, 24) }}
                            </div>
                        @endif
                        @if ($task->assignees->isNotEmpty())
                            <div style="margin-top:6px">
                                @foreach ($task->assignees as $assignee)
                                    <span class="admin-chip">{{ $assignee->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        @can('tasks.edit')
                            <form action="{{ route('admin.tasks.status', $task) }}" method="POST" style="margin-top:8px">
                                @csrf @method('PATCH')
                                <select name="status" class="admin-select" onchange="this.form.submit()" style="font-size:12px; padding:5px 8px;">
                                    @foreach (\App\Models\Task::STATUSES as $value => $optionLabel)
                                        <option value="{{ $value }}" @selected($task->status === $value)>Move to {{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endcan
                    </div>
                @empty
                    <div class="admin-mini-list__empty" style="font-size:13px">Nothing here.</div>
                @endforelse
            </div>
        @endforeach
    </div>

@endsection
