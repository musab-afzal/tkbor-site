<?php

namespace App\Notifications;

use App\Models\Task;

class TaskAssigned extends AdminNotification
{
    public function __construct(protected Task $task) {}

    protected function payload(): array
    {
        return [
            'title' => 'Task assigned: '.$this->task->title,
            'message' => $this->task->due_date
                ? 'Due '.$this->task->due_date->format('M d, Y')
                : 'No due date set',
            'icon' => 'fa-list-check',
            'url' => route('admin.tasks.show', $this->task),
        ];
    }
}
