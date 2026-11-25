<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Brings the original CMS tables in line with the rest of the panel so a
 * deleted service or post can be recovered instead of being lost.
 */
return new class extends Migration
{
    /** @var array<int, string> */
    protected array $tables = ['services', 'companies', 'portfolios', 'reviews', 'faqs', 'team_members', 'blogs'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->softDeletes());
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('deleted_at'));
            }
        }
    }
};
