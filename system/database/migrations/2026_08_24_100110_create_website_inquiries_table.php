<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_number')->unique();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('service')->nullable();
            $table->string('budget')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('source_page')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('status')->default('new')->index();
            $table->string('priority')->default('medium')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->foreignId('converted_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('converted_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_inquiries');
    }
};
