<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'employee_id', 'date', 'check_in', 'check_out', 'worked_hours', 'status', 'notes',
])]
class Attendance extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'half_day' => 'Half Day',
        'leave' => 'On Leave',
        'holiday' => 'Holiday',
        'remote' => 'Remote',
    ];

    protected function casts(): array
    {
        return ['date' => 'date', 'worked_hours' => 'decimal:2'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function activityLabel(): string
    {
        return ($this->employee?->name ?? 'Employee').' on '.$this->date?->format('M d, Y');
    }
}
