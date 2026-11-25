<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>|null  $changes
     */
    public function record(string $action, ?Model $subject = null, ?string $description = null, ?array $changes = null): ActivityLog
    {
        $admin = Auth::guard('admin')->user();

        return ActivityLog::create([
            // An admin deleting their own account leaves no row to point at,
            // so keep the name for the trail but drop the foreign key.
            'admin_id' => $admin?->exists ? $admin->getKey() : null,
            'admin_name' => $admin?->name,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'subject_label' => $subject && method_exists($subject, 'activityLabel')
                ? $subject->activityLabel()
                : null,
            'description' => $description ?? $this->describe($action, $subject),
            'changes' => $changes,
            'ip_address' => Request::ip(),
        ]);
    }

    protected function describe(string $action, ?Model $subject): string
    {
        if (! $subject) {
            return ucfirst(str_replace('_', ' ', $action));
        }

        $label = method_exists($subject, 'activityLabel')
            ? $subject->activityLabel()
            : class_basename($subject).' #'.$subject->getKey();

        return ucfirst(str_replace('_', ' ', $action)).' '.class_basename($subject).': '.$label;
    }
}
