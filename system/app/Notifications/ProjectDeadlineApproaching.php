<?php

namespace App\Notifications;

use App\Models\Project;

class ProjectDeadlineApproaching extends AdminNotification
{
    public function __construct(protected Project $project) {}

    protected function payload(): array
    {
        return [
            'title' => 'Deadline approaching: '.$this->project->name,
            'message' => 'Due '.$this->project->deadline?->format('M d, Y'),
            'icon' => 'fa-flag-checkered',
            'url' => route('admin.projects.show', $this->project),
        ];
    }
}
