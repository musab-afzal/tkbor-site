<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/*
 * The original `projects` table stores website portfolio entries (slug,
 * featured image, is_featured, project_url). The agency delivery module needs
 * the `projects` name for real client projects, so the CMS table is renamed to
 * `portfolios`. Schema::rename preserves every existing row.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects') && ! Schema::hasTable('portfolios')) {
            Schema::rename('projects', 'portfolios');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('portfolios') && ! Schema::hasTable('projects')) {
            Schema::rename('portfolios', 'projects');
        }
    }
};
