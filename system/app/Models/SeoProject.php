<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'client_id', 'project_id', 'website', 'target_country', 'start_date',
    'goals', 'notes', 'status', 'managed_by',
])]
class SeoProject extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'active' => 'Active',
        'paused' => 'Paused',
        'completed' => 'Completed',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'managed_by');
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }
}
