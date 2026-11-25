<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lead_id', 'admin_id', 'type', 'title', 'description', 'old_status', 'new_status',
])]
class LeadActivity extends Model
{
    public const TYPES = [
        'note' => 'Note',
        'call' => 'Call',
        'email' => 'Email',
        'meeting' => 'Meeting',
        'status_change' => 'Status Change',
        'converted' => 'Converted',
        'assignment' => 'Assignment',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
