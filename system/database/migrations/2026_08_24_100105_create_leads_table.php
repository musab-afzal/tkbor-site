<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_number')->unique();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('source')->nullable()->index();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('interested_service')->nullable();
            $table->decimal('estimated_budget', 14, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->date('next_follow_up_at')->nullable()->index();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->text('lost_reason')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('note')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
    }
};
