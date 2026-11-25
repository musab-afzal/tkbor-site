<?php

namespace App\Notifications;

use App\Models\Ticket;

class TicketAssigned extends AdminNotification
{
    public function __construct(protected Ticket $ticket) {}

    protected function payload(): array
    {
        return [
            'title' => 'Ticket assigned: '.$this->ticket->subject,
            'message' => $this->ticket->ticket_number.' — '.($this->ticket->client?->displayName() ?? 'No client'),
            'icon' => 'fa-headset',
            'url' => route('admin.tickets.show', $this->ticket),
        ];
    }
}
