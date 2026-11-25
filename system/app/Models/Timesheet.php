<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'employee_id', 'project_id', 'task_id', 'date', 'start_time', 'end_time',
    'hours', 'description', 'is_billable', 'hourly_rate', 'status',
])]
class Timesheet extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'hours' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'is_billable' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Derive hours from the clock times whenever both are supplied.
        $derive = function (Timesheet $sheet) {
            if ($sheet->start_time && $sheet->end_time) {
                $start = Carbon::parse($sheet->start_time);
                $end = Carbon::parse($sheet->end_time);

                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $sheet->hours = round($start->floatDiffInHours($end), 2);
            }
        };

        static::creating($derive);
        static::updating($derive);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function billableValue(): float
    {
        return $this->is_billable
            ? round((float) $this->hours * (float) ($this->hourly_rate ?? $this->employee?->hourly_rate ?? 0), 2)
            : 0.0;
    }

    public function activityLabel(): string
    {
        return ($this->employee?->name ?? 'Employee').' — '.$this->hours.'h';
    }
}
