<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Delivery projects. The website portfolio lives in `portfolios` (renamed from
 * the original `projects` table) and stays a separate CMS concern.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_number')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('project_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->json('technologies')->nullable();
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable()->index();
            $table->date('completed_at')->nullable();
            $table->decimal('budget', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->unsignedTinyInteger('progress')->default(0);
            // The renamed portfolio table retains projects_status_index on SQLite.
            $table->string('status')->default('planning')->index('delivery_projects_status_index');
            $table->string('priority')->default('medium')->index();
            $table->string('repository_url')->nullable();
            $table->string('live_url')->nullable();
            $table->string('staging_url')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->unique(['project_id', 'employee_id']);
        });

        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable()->index();
            $table->decimal('amount', 14, 2)->nullable();
            $table->boolean('is_payment_milestone')->default(false);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('title');
            $table->string('requirement_type')->default('functional')->index();
            $table->text('description')->nullable();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('draft')->index();
            $table->boolean('client_approved')->default(false);
            $table->date('client_approved_at')->nullable();
            $table->string('version')->default('1.0');
            $table->text('change_log')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('requested_by')->nullable();
            $table->text('description')->nullable();
            $table->text('reason')->nullable();
            $table->decimal('cost_impact', 14, 2)->default(0);
            $table->smallInteger('timeline_impact_days')->default(0);
            $table->string('status')->default('pending')->index();
            $table->boolean('client_approved')->default(false);
            $table->date('client_approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bugs', function (Blueprint $table) {
            $table->id();
            $table->string('bug_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('steps_to_reproduce')->nullable();
            $table->string('severity')->default('minor')->index();
            $table->string('priority')->default('medium')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('status')->default('open')->index();
            $table->text('resolution')->nullable();
            $table->date('reported_date')->nullable();
            $table->date('resolved_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bugs');
        Schema::dropIfExists('change_requests');
        Schema::dropIfExists('project_requirements');
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('project_employee');
        Schema::dropIfExists('projects');
    }
};
