<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform')->index();
            $table->string('objective')->nullable();
            $table->decimal('budget', 14, 2)->default(0);
            $table->decimal('spent', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable()->index();
            $table->unsignedBigInteger('impressions')->nullable();
            $table->unsignedBigInteger('clicks')->nullable();
            $table->unsignedBigInteger('conversions')->nullable();
            $table->text('results')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('planned')->index();
            $table->foreignId('managed_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('seo_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('website');
            $table->string('target_country')->nullable();
            $table->date('start_date')->nullable();
            $table->text('goals')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('managed_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_project_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->string('target_url')->nullable();
            $table->unsignedInteger('search_volume')->nullable();
            $table->unsignedSmallInteger('current_rank')->nullable();
            $table->unsignedSmallInteger('previous_rank')->nullable();
            $table->date('last_checked_at')->nullable();
            $table->string('difficulty')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('tracking')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform')->index();
            $table->text('content')->nullable();
            $table->string('media')->nullable();
            $table->string('link')->nullable();
            $table->dateTime('scheduled_at')->index();
            $table->dateTime('published_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('seo_projects');
        Schema::dropIfExists('campaigns');
    }
};
