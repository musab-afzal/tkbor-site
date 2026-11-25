<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('website_inquiry_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('scheduled_at')->index();
            $table->string('type')->default('call')->index();
            $table->text('notes')->nullable();
            $table->text('result')->nullable();
            $table->string('status')->default('pending')->index();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('scheduled_at')->index();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('mode')->default('online')->index();
            $table->string('location')->nullable();
            $table->string('meeting_url')->nullable();
            $table->text('agenda')->nullable();
            $table->text('notes')->nullable();
            $table->text('outcome')->nullable();
            $table->text('next_action')->nullable();
            $table->string('status')->default('scheduled')->index();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->boolean('attended')->default(false);
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('project')->index();
            $table->decimal('value', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('scope')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft')->index();
            $table->date('signed_date')->nullable();
            $table->string('signed_by')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('follow_ups');
    }
};
