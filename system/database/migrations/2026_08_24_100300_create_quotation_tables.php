<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->unsignedSmallInteger('revision')->default(0);
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('website_inquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_name');
            $table->string('project_type')->nullable();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable()->index();
            $table->string('currency', 3)->default('USD');

            $table->text('project_summary')->nullable();
            $table->text('requirements')->nullable();
            $table->text('scope')->nullable();
            $table->text('proposed_solution')->nullable();
            $table->json('technologies')->nullable();
            $table->text('project_timeline')->nullable();
            $table->text('deliverables')->nullable();
            $table->unsignedSmallInteger('revisions_included')->nullable();
            $table->string('support_period')->nullable();
            $table->text('hosting_domain_info')->nullable();
            $table->text('third_party_charges')->nullable();
            $table->text('client_responsibilities')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('internal_notes')->nullable();

            $table->string('discount_type')->default('percentage');
            $table->decimal('discount_value', 14, 2)->default(0);
            $table->decimal('tax_percentage', 6, 2)->default(0);

            // Cached money columns, always recalculated by QuotationCalculator.
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->decimal('free_services_value', 14, 2)->default(0);
            $table->decimal('optional_items_value', 14, 2)->default(0);
            $table->decimal('internal_cost_total', 14, 2)->default(0);
            $table->unsignedSmallInteger('total_estimated_days')->default(0);

            $table->string('status')->default('draft')->index();
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->string('client_accepted_by')->nullable();
            $table->date('client_accepted_date')->nullable();
            $table->text('client_remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pricing_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('section')->default('main')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->decimal('internal_cost', 14, 2)->nullable();
            $table->unsignedSmallInteger('estimated_days')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_free')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quotation_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('description')->nullable();
            $table->decimal('percentage', 6, 2)->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('due_condition')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_milestones');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
