<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wordpress_site_id')->constrained()->cascadeOnDelete();

            // Brief — the input the user fills in for the AI.
            $table->string('topic');
            $table->string('page_type')->default('landing');
            $table->string('audience')->nullable();
            $table->string('tone')->nullable();
            $table->string('language', 8)->default('ru');
            $table->string('keywords')->nullable();
            $table->text('sections')->nullable();
            $table->string('cta')->nullable();
            $table->text('extra_instructions')->nullable();

            // AI output — logical field name => value (seo_* + block*_*).
            $table->json('fields')->nullable();

            // WordPress meta the user picks before pushing.
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->json('category_ids')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->integer('menu_order')->default(0);

            // Workflow.
            $table->string('status')->default('generated'); // generated | pushed | failed
            $table->unsignedBigInteger('wp_post_id')->nullable();
            $table->string('wp_edit_link')->nullable();
            $table->string('wp_preview_link')->nullable();
            $table->text('error')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_pages');
    }
};
