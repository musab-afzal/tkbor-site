<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('category')->default('general')->index();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('open')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium')->index();
            $table->decimal('estimated_cost', 14, 2)->default(0);
            $table->decimal('approved_cost', 14, 2)->nullable();
            $table->unsignedSmallInteger('estimated_hours')->nullable();
            $table->string('status')->default('pending')->index();
            $table->boolean('client_approved')->default(false);
            $table->date('client_approved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('approvable_type')->nullable();
            $table->unsignedBigInteger('approvable_id')->nullable();
            $table->string('item_type')->default('design')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('sent_date')->nullable()->index();
            $table->date('responded_date')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('client_remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['approvable_type', 'approvable_id']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('name');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->nullable();
            $table->string('provider_url')->nullable();
            $table->string('account_reference')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->decimal('cost', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle')->default('yearly')->index();
            $table->boolean('auto_renew')->default(false);
            $table->unsignedSmallInteger('reminder_days')->default(30);
            $table->string('status')->default('active')->index();
            $table->text('credentials_note')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
        Schema::dropIfExists('client_approvals');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
    }
};
