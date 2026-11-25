<?php

namespace App\Http\Controllers\Admin;

use App\Models\Meeting;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeetingController extends AdminResourceController
{
    protected string $model = Meeting::class;

    protected string $route = 'meetings';

    protected string $permission = 'meetings';

    protected string $singular = 'Meeting';

    protected string $plural = 'Meetings';

    protected string $description = 'Client and lead meetings with agenda, participants and outcomes.';

    protected string $icon = 'fa-handshake';

    protected array $searchable = ['title', 'agenda', 'location'];

    protected array $with = ['lead', 'client', 'participants'];

    protected array $relationFields = ['participants'];

    protected string $defaultSort = 'scheduled_at';

    protected string $defaultDirection = 'desc';

    protected function columns(): array
    {
        return [
            Column::text('title', 'Title'),
            Column::custom('with', 'With', fn (Meeting $meeting) => e(
                $meeting->client?->displayName() ?? $meeting->lead?->name ?? '—'
            )),
            Column::datetime('scheduled_at', 'Date & Time'),
            Column::badge('mode', 'Mode', Meeting::MODES),
            Column::custom('participants', 'Participants', fn (Meeting $meeting) => $meeting->participants->isEmpty()
                ? '—'
                : collect($meeting->participants)->map(fn ($p) => '<span class="admin-chip">'.e($p->name).'</span>')->implode('')),
            Column::badge('status', 'Status', Meeting::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Meeting'),
            Field::text('title', 'Title', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('lead_id', 'Lead', $this->options->leads()),
            Field::datetime('scheduled_at', 'Date & Time', ['required' => true]),
            Field::number('duration_minutes', 'Duration (minutes)', ['placeholder' => '60']),
            Field::select('mode', 'Mode', Meeting::MODES, ['required' => true]),
            Field::select('status', 'Status', Meeting::STATUSES, ['required' => true]),
            Field::text('location', 'Location', ['help' => 'For physical meetings.']),
            Field::url('meeting_url', 'Meeting URL', ['help' => 'Zoom, Meet or Teams link for online meetings.']),
            Field::multiselect('participants', 'Internal Participants', $this->options->employees()),

            Field::heading('Detail'),
            Field::textarea('agenda', 'Agenda'),
            Field::textarea('notes', 'Notes'),
            Field::textarea('outcome', 'Outcome'),
            Field::textarea('next_action', 'Next Action'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Meeting::STATUSES],
            ['key' => 'mode', 'label' => 'Mode', 'options' => Meeting::MODES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'scheduled_at'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'scheduled_at'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'mode' => ['required', Rule::in(array_keys(Meeting::MODES))],
            'status' => ['required', Rule::in(array_keys(Meeting::STATUSES))],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_url' => ['nullable', 'url', 'max:255'],
            'participants' => ['array'],
            'participants.*' => ['exists:employees,id'],
            'agenda' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'outcome' => ['nullable', 'string'],
            'next_action' => ['nullable', 'string'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        /** @var Meeting $record */
        $record->participants()->sync($request->input('participants', []));
    }
}
