<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Foreign keys that close the conversion chain. They live in their own
 * migration because the tables they point at are created in both directions
 * (inquiry -> lead -> client -> quotation -> project).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('website_inquiry_id')->nullable()->after('source')->constrained()->nullOnDelete();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('type')->constrained()->nullOnDelete();
        });

        Schema::table('website_inquiries', function (Blueprint $table) {
            $table->foreignId('converted_quotation_id')->nullable()->after('converted_client_id')->constrained('quotations')->nullOnDelete();
            $table->foreignId('converted_project_id')->nullable()->after('converted_quotation_id')->constrained('projects')->nullOnDelete();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('converted_project_id')->nullable()->after('decided_at')->constrained('projects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['converted_project_id']);
            $table->dropColumn('converted_project_id');
        });

        Schema::table('website_inquiries', function (Blueprint $table) {
            $table->dropForeign(['converted_quotation_id']);
            $table->dropForeign(['converted_project_id']);
            $table->dropColumn(['converted_quotation_id', 'converted_project_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropColumn('lead_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['website_inquiry_id']);
            $table->dropColumn('website_inquiry_id');
        });
    }
};
