<?php

namespace App\Notifications;

use App\Models\Task;

class TaskDueReminder extends AdminNotification
{
    public function __construct(protected Task $task, protected bool $overdue = false) {}

    protected function payload(): array
    {
        return [
            'title' => ($this->overdue ? 'Overdue task: ' : 'Task due today: ').$this->task->title,
            'message' => $this->task->project?->name ?? 'General task',
            'icon' => $this->overdue ? 'fa-triangle-exclamation' : 'fa-calendar-day',
            'url' => route('admin.tasks.show', $this->task),
        ];
    }
}
