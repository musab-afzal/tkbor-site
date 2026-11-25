<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * The existing `clients` table already held name, company, email, phone,
 * website, logo and status. It is extended in place into the full CRM client
 * record so the website "our clients" data and the CRM stay one source of
 * truth. Existing rows keep their values; every new column is nullable or
 * defaulted.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_code')->nullable()->unique()->after('id');
            $table->string('type')->default('company')->index()->after('client_code');
            $table->string('contact_person')->nullable()->after('company_name');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->text('address')->nullable()->after('whatsapp');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('country');
            $table->string('tax_number')->nullable()->after('postal_code');
            $table->string('tax_name')->nullable()->after('tax_number');
            $table->string('industry')->nullable()->after('tax_name');
            $table->string('currency', 3)->nullable()->after('industry');
            $table->text('notes')->nullable()->after('description');
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'client_code', 'type', 'contact_person', 'whatsapp', 'address', 'city',
                'country', 'postal_code', 'tax_number', 'tax_name', 'industry',
                'currency', 'notes', 'created_by', 'updated_by', 'deleted_at',
            ]);
        });
    }
};
