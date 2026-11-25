<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ticket;
use App\Notifications\TicketAssigned;
use App\Services\NotificationDispatcher;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends AdminResourceController
{
    protected string $model = Ticket::class;

    protected string $route = 'tickets';

    protected string $permission = 'tickets';

    protected string $singular = 'Ticket';

    protected string $plural = 'Support Tickets';

    protected string $description = 'Client support requests with their full reply history.';

    protected string $icon = 'fa-headset';

    protected array $searchable = ['ticket_number', 'subject', 'description', 'contact_name'];

    protected array $with = ['client', 'project', 'assignee'];

    protected bool $hasShow = true;

    protected function columns(): array
    {
        return [
            Column::code('ticket_number', 'Ticket #'),
            Column::text('subject', 'Subject'),
            Column::relation('client.company_name', 'Client'),
            Column::badge('category', 'Category', Ticket::CATEGORIES),
            Column::badge('priority', 'Priority', Ticket::PRIORITIES),
            Column::relation('assignee.name', 'Assigned To'),
            Column::badge('status', 'Status', Ticket::STATUSES),
            Column::date('created_at', 'Opened'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('subject', 'Subject', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('project_id', 'Project', $this->options->projects()),
            Field::select('category', 'Category', Ticket::CATEGORIES, ['required' => true]),
            Field::select('priority', 'Priority', Ticket::PRIORITIES, ['required' => true]),
            Field::select('status', 'Status', Ticket::STATUSES, ['required' => true]),
            Field::select('assigned_to', 'Assigned Employee', $this->options->employees()),
            Field::text('contact_name', 'Contact Name'),
            Field::email('contact_email', 'Contact Email'),
            Field::textarea('description', 'Description', ['rows' => 5]),
            Field::textarea('resolution', 'Resolution'),
            Field::file('attachment', 'Attachment', ['attachment' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Ticket::STATUSES],
            ['key' => 'priority', 'label' => 'Priority', 'options' => Ticket::PRIORITIES],
            ['key' => 'category', 'label' => 'Category', 'options' => Ticket::CATEGORIES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'assigned_to', 'label' => 'Assignee', 'options' => $this->options->employees()],
            ['key' => 'open', 'label' => 'View', 'options' => ['1' => 'Open only']],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return $request->boolean('open') ? $query->open() : $query;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'category' => ['required', Rule::in(array_keys(Ticket::CATEGORIES))],
            'priority' => ['required', Rule::in(array_keys(Ticket::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Ticket::STATUSES))],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string'],
            'resolution' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'tickets');
        }

        /** @var Ticket $record */
        if ($record->assigned_to && ($created || $record->wasChanged('assigned_to'))) {
            app(NotificationDispatcher::class)->toEmployees([$record->assigned_to], new TicketAssigned($record));
        }
    }

    public function show(Ticket $ticket): View
    {
        $this->authorizeAction('view');

        $ticket->load(['client', 'project', 'assignee', 'attachments', 'replies.admin']);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'employees' => $this->options->employees(),
        ]);
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('tickets.reply');

        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
            'is_internal' => ['boolean'],
            'status' => ['nullable', Rule::in(array_keys(Ticket::STATUSES))],
        ]);

        $ticket->replies()->create([
            'admin_id' => Auth::guard('admin')->id(),
            'author_name' => Auth::guard('admin')->user()?->name,
            'body' => $data['body'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        $updates = [];

        if (! $ticket->first_response_at && ! $request->boolean('is_internal')) {
            $updates['first_response_at'] = now();
        }

        if ($request->filled('status')) {
            $updates['status'] = $data['status'];
        }

        if ($updates !== []) {
            $ticket->update($updates);
        }

        return back()->with('success', 'Reply added to the ticket.');
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorizeAction('edit');

        $ticket->update($request->validate([
            'assigned_to' => ['nullable', 'exists:employees,id'],
        ]));

        if ($ticket->assigned_to && $ticket->wasChanged('assigned_to')) {
            app(NotificationDispatcher::class)->toEmployees([$ticket->assigned_to], new TicketAssigned($ticket));
        }

        return back()->with('success', 'Ticket assignment updated.');
    }
}
