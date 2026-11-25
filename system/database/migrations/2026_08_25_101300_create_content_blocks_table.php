<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Small repeating pieces of website copy that used to be hard-coded in the
 * templates: the counters, the "why choose us" cards, the about highlights and
 * the scrolling marquee. One table keeps them editable without a module per
 * section.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('content_blocks')) {
            return;
        }

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('section')->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            // Counters keep their number and unit apart so "28" + "k+" can be
            // animated by the template's count-up script.
            $table->string('value')->nullable();
            $table->string('suffix')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
