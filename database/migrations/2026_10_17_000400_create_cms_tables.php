<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per managed item: a page, a service, an article or a group of site settings.
        Schema::create('cms_entries', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();          // e.g. "home", "page:about", "service:subtitling"
            $table->string('type', 20)->index();      // home | page | service | article | settings
            $table->string('slug')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // The content of an entry in one language: the live (published) version and a pending draft.
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_entry_id')->constrained('cms_entries')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->json('published')->nullable();
            $table->json('draft')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('draft_updated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['cms_entry_id', 'locale']);
        });

        // Every published version, so earlier content can be restored.
        Schema::create('cms_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_entry_id')->constrained('cms_entries')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->json('content');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
            $table->index(['cms_entry_id', 'locale']);
        });

        // Edited interface text (headings, buttons and labels written in the page templates).
        Schema::create('cms_strings', function (Blueprint $table) {
            $table->id();
            $table->char('key_hash', 40)->unique();
            $table->text('key');
            $table->text('en')->nullable();
            $table->text('fr')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_en')->nullable();
            $table->string('alt_fr')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('cms_strings');
        Schema::dropIfExists('cms_revisions');
        Schema::dropIfExists('cms_contents');
        Schema::dropIfExists('cms_entries');
    }
};
