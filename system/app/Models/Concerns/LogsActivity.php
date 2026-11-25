<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Writes create / update / delete entries to the audit trail. Models may
 * override activityLabel() to give the log a human readable subject.
 */
trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            app(ActivityLogger::class)->record('created', $model);
        });

        static::updated(function (Model $model) {
            $changes = collect($model->getChanges())
                ->except(['updated_at', 'updated_by', 'remember_token', 'password'])
                ->all();

            if ($changes === []) {
                return;
            }

            app(ActivityLogger::class)->record('updated', $model, changes: $changes);
        });

        static::deleted(function (Model $model) {
            app(ActivityLogger::class)->record('deleted', $model);
        });
    }

    public function activityLabel(): string
    {
        foreach (['name', 'title', 'subject', 'quotation_number', 'invoice_number', 'project_name'] as $column) {
            if (! empty($this->{$column})) {
                return (string) $this->{$column};
            }
        }

        return class_basename($this).' #'.$this->getKey();
    }
}
