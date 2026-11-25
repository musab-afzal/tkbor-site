<?php

namespace App\Http\Controllers\Admin;

use App\Models\FollowUp;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FollowUpController extends AdminResourceController
{
    protected string $model = FollowUp::class;

    protected string $route = 'follow-ups';

    protected string $permission = 'follow_ups';

    protected string $singular = 'Follow Up';

    protected string $plural = 'Follow Ups';

    protected string $description = 'Scheduled calls, messages and emails with leads and clients.';

    protected string $icon = 'fa-phone-volume';

    protected array $searchable = ['notes', 'result'];

    protected array $with = ['lead', 'client', 'websiteInquiry', 'assignee'];

    protected string $defaultSort = 'scheduled_at';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::custom('subject', 'Regarding', fn (FollowUp $followUp) => e($followUp->subjectName())),
            Column::badge('type', 'Type', FollowUp::TYPES),
            Column::datetime('scheduled_at', 'Scheduled'),
            Column::relation('assignee.name', 'Assigned To'),
            Column::custom('state', 'State', function (FollowUp $followUp) {
                if ($followUp->isOverdue()) {
                    return '<span class="admin-badge admin-badge--overdue">Overdue</span>';
                }

                $label = FollowUp::STATUSES[$followUp->status] ?? $followUp->status;

                return '<span class="admin-badge admin-badge--'.e($followUp->status).'">'.e($label).'</span>';
            }),
            Column::datetime('next_follow_up_at', 'Next'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Regarding'),
            Field::select('lead_id', 'Lead', $this->options->leads(), [
                'help' => 'Pick a lead, a client, or leave both blank for a general follow-up.',
            ]),
            Field::select('client_id', 'Client', $this->options->clients()),

            Field::heading('Schedule'),
            Field::select('type', 'Type', FollowUp::TYPES, ['required' => true]),
            Field::datetime('scheduled_at', 'Scheduled At', ['required' => true]),
            Field::select('assigned_to', 'Assigned Employee', $this->options->employees()),
            Field::select('status', 'Status', FollowUp::STATUSES, ['required' => true]),
            Field::datetime('next_follow_up_at', 'Next Follow-up'),

            Field::heading('Detail'),
            Field::textarea('notes', 'Notes / Talking Points'),
            Field::textarea('result', 'Result'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => FollowUp::STATUSES],
            ['key' => 'type', 'label' => 'Type', 'options' => FollowUp::TYPES],
            ['key' => 'assigned_to', 'label' => 'Assignee', 'options' => $this->options->employees()],
            ['key' => 'view', 'label' => 'Due', 'options' => [
                'today' => 'Due Today',
                'overdue' => 'Overdue',
                'upcoming' => 'Upcoming',
            ]],
        ];
    }

    /** The "Due" filter is a scope rather than a plain column comparison. */
    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return match ($request->string('view')->toString()) {
            'today' => $query->dueToday(),
            'overdue' => $query->overdue(),
            'upcoming' => $query->pending()->where('scheduled_at', '>', now()),
            default => $query,
        };
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'lead_id' => ['nullable', 'exists:leads,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'type' => ['required', Rule::in(array_keys(FollowUp::TYPES))],
            'scheduled_at' => ['required', 'date'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'status' => ['required', Rule::in(array_keys(FollowUp::STATUSES))],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
        ];
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        // Stamp the completion time the moment the status flips to completed.
        if (($data['status'] ?? null) === FollowUp::STATUS_COMPLETED) {
            $data['completed_at'] = $record->completed_at ?? now();
        } else {
            $data['completed_at'] = null;
        }

        return $data;
    }

    /** One-click complete from the listing or the dashboard widget. */
    public function complete(FollowUp $followUp): RedirectResponse
    {
        $this->authorizeAction('edit');

        $followUp->update([
            'status' => FollowUp::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Follow up marked as completed.');
    }
}
