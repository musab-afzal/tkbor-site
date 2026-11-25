<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client_name')->nullable();
            $table->string('category')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('project_url')->nullable();
            $table->json('technologies')->nullable();
            $table->date('project_date')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
