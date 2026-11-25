<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/*
 * Eloquent derives a belongsToMany table name by sorting the two model names
 * alphabetically, so the pivots must be employee_project and employee_task.
 */
return new class extends Migration
{
    /** @var array<string, string> */
    protected array $renames = [
        'project_employee' => 'employee_project',
        'task_employee' => 'employee_task',
    ];

    public function up(): void
    {
        foreach ($this->renames as $from => $to) {
            if (Schema::hasTable($from) && ! Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->renames as $from => $to) {
            if (Schema::hasTable($to) && ! Schema::hasTable($from)) {
                Schema::rename($to, $from);
            }
        }
    }
};
