<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'admin_id', 'admin_name', 'action', 'subject_type', 'subject_id',
    'subject_label', 'description', 'changes', 'ip_address',
])]
class ActivityLog extends Model
{
    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForSubject(Builder $query, Model $model): Builder
    {
        return $query->where('subject_type', $model::class)->where('subject_id', $model->getKey());
    }
}
