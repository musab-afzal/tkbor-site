<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'client_id', 'type', 'value', 'currency', 'start_date', 'end_date',
    'scope', 'terms', 'notes', 'status', 'signed_date', 'signed_by',
])]
class Contract extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const TYPES = [
        'project' => 'Project',
        'retainer' => 'Retainer',
        'maintenance' => 'Maintenance',
        'nda' => 'NDA',
        'sla' => 'SLA',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'signed' => 'Signed',
        'active' => 'Active',
        'expired' => 'Expired',
        'terminated' => 'Terminated',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'signed_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Contract $contract) {
            $contract->contract_number = $contract->contract_number
                ?: app(DocumentNumberGenerator::class)->next('contracts', 'contract_number', 'contract_prefix', 'CT');
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function activityLabel(): string
    {
        return $this->contract_number.' — '.$this->title;
    }
}
