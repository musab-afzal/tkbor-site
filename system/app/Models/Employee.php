<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'employee_code', 'admin_id', 'department_id', 'name', 'photo', 'email', 'phone',
    'whatsapp', 'designation', 'joining_date', 'exit_date', 'employment_type',
    'salary', 'hourly_rate', 'skills', 'address', 'notes', 'status',
])]
class Employee extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_ACTIVE = 'active';

    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'on_leave' => 'On Leave',
        'resigned' => 'Resigned',
    ];

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'contract' => 'Contract',
        'intern' => 'Intern',
        'freelance' => 'Freelance',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'joining_date' => 'date',
            'exit_date' => 'date',
            'salary' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)->withPivot('is_primary');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withPivot('role');
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
