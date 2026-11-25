<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pricing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('detailed_features')->nullable();
            $table->decimal('default_price', 14, 2)->default(0);
            $table->decimal('min_price', 14, 2)->nullable();
            $table->decimal('max_price', 14, 2)->nullable();
            $table->decimal('internal_cost', 14, 2)->nullable();
            $table->string('unit')->default('project');
            $table->unsignedSmallInteger('estimated_days')->nullable();
            $table->json('technologies')->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->boolean('discount_allowed')->default(true);
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_free')->default(false);
            $table->json('addons')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tier')->default('standard')->index();
            $table->string('short_description')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('discount_price', 14, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('billing_period')->nullable();
            $table->json('features')->nullable();
            $table->unsignedSmallInteger('delivery_days')->nullable();
            $table->unsignedSmallInteger('revisions')->nullable();
            $table->boolean('is_recommended')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
        Schema::dropIfExists('pricing_items');
        Schema::dropIfExists('pricing_categories');
    }
};
