<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->json('benefits')->nullable()->after('description');
            $table->json('features')->nullable()->after('benefits');
            $table->string('cta_label')->nullable()->after('features');
            $table->string('cta_url')->nullable()->after('cta_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['benefits', 'features', 'cta_label', 'cta_url']);
        });
    }
};
