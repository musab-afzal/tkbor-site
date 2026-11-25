<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SaaS storefront: software products customers can trial, and one
 * subscription row per customer + product holding the access window,
 * the credentials the admin hands out and the latest payment submission.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('features')->nullable();
            $table->string('app_url')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 3)->nullable();
            $table->string('billing_period')->default('month');
            $table->unsignedSmallInteger('billing_days')->default(30);
            $table->unsignedSmallInteger('trial_days')->default(7);
            $table->boolean('status')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('saas_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('saas_product_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('trial')->index();
            $table->string('license_key', 64)->unique();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();

            $table->string('access_url')->nullable();
            $table->string('access_username')->nullable();
            $table->text('access_password')->nullable();

            $table->string('payment_status', 20)->nullable()->index();
            $table->string('payment_method', 100)->nullable();
            $table->string('payment_reference', 191)->nullable();
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->string('payment_receipt')->nullable();
            $table->text('payment_note')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'saas_product_id']);
        });

        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 50)->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_subscriptions');
        Schema::dropIfExists('saas_products');

        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};
