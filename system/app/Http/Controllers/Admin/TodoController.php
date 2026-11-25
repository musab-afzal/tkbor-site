<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Todo;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Lightweight internal checklist, deliberately separate from project tasks.
 */
class TodoController extends AdminResourceController
{
    protected string $model = Todo::class;

    protected string $route = 'todos';

    protected string $permission = 'todos';

    protected string $singular = 'TODO';

    protected string $plural = 'Company TODO';

    protected string $description = 'Quick internal to-dos that are not formal project tasks.';

    protected string $icon = 'fa-clipboard-check';

    protected array $searchable = ['title', 'description'];

    protected array $with = ['creator', 'assignee'];

    protected array $relationFields = ['checklist'];

    protected bool $hasShow = true;

    protected string $defaultSort = 'due_date';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('title', 'Title'),
            Column::relation('assignee.name', 'Assigned To'),
            Column::relation('creator.name', 'Created By'),
            Column::custom('due', 'Due', function (Todo $todo) {
                if (! $todo->due_date) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                $label = e($todo->due_date->format('M d, Y'));

                return $todo->isOverdue()
                    ? '<span class="admin-badge admin-badge--overdue">'.$label.'</span>'
                    : $label;
            }),
            Column::badge('priority', 'Priority', Todo::PRIORITIES),
            Column::badge('status', 'Status', Todo::STATUSES),
            Column::boolean('is_company_wide', 'Company Wide'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('assigned_to', 'Assign To', $this->adminOptions(), ['placeholder' => 'Nobody in particular']),
            Field::date('date', 'Date'),
            Field::date('due_date', 'Due Date'),
            Field::select('priority', 'Priority', Todo::PRIORITIES, ['required' => true]),
            Field::select('status', 'Status', Todo::STATUSES, ['required' => true]),
            Field::checkbox('is_company_wide', 'Visible to everyone'),
            Field::textarea('description', 'Description'),
            Field::textarea('notes', 'Notes'),
            Field::textarea('checklist', 'Checklist', [
                'rows' => 5,
                'help' => 'One item per line. Existing ticks are preserved where the text matches.',
                'value' => $this->currentRecord?->exists
                    ? $this->currentRecord->items->pluck('title')->implode("\n")
                    : '',
            ]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Todo::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => Todo::PRIORITIES],
            ['key' => 'assigned_to', 'label' => 'Assignee', 'options' => $this->adminOptions()],
            ['key' => 'view', 'label' => 'View', 'options' => [
                'mine' => 'My TODOs',
                'company' => 'Company TODOs',
                'today' => 'Today',
                'upcoming' => 'Upcoming',
                'completed' => 'Completed',
            ]],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        $admin = Auth::guard('admin')->user();

        return match ($request->string('view')->toString()) {
            'mine' => $query->where(fn (Builder $q) => $q->where('created_by', $admin?->getKey())
                ->orWhere('assigned_to', $admin?->getKey())),
            'company' => $query->where('is_company_wide', true),
            'today' => $query->open()->whereDate('due_date', today()),
            'upcoming' => $query->open()->whereDate('due_date', '>', today()),
            'completed' => $query->where('status', Todo::STATUS_COMPLETED),
            default => $query->visibleTo($admin),
        };
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:admins,id'],
            'date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(array_keys(Todo::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Todo::STATUSES))],
            'is_company_wide' => ['boolean'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'checklist' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** Rebuilds the checklist, keeping the done state of items that still exist. */
    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        /** @var Todo $record */
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('checklist')))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        $done = $record->items()->pluck('is_done', 'title');

        $record->items()->delete();

        foreach ($lines as $index => $title) {
            $record->items()->create([
                'title' => $title,
                'is_done' => (bool) ($done[$title] ?? false),
                'sort_order' => $index,
            ]);
        }
    }

    public function show(Todo $todo): View
    {
        $this->authorizeAction('view');

        $todo->load(['items', 'creator', 'assignee']);

        return view('admin.todos.show', compact('todo'));
    }

    public function complete(Todo $todo): RedirectResponse
    {
        $this->authorizeAction('edit');

        $todo->update(['status' => Todo::STATUS_COMPLETED]);

        return back()->with('success', 'TODO marked as completed.');
    }

    public function toggleItem(Todo $todo, int $item): RedirectResponse
    {
        $this->authorizeAction('edit');

        $todoItem = $todo->items()->findOrFail($item);
        $todoItem->update(['is_done' => ! $todoItem->is_done]);

        return back();
    }

    /** @return array<int, string> */
    protected function adminOptions(): array
    {
        return Admin::where('status', true)->orderBy('name')->pluck('name', 'id')->all();
    }
}
