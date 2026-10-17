<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Linguist network applications (Careers page form in the content document).
     */
    public function up(): void
    {
        Schema::create('linguist_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('location', 160);
            $table->json('native_languages');
            $table->string('language_pairs', 500);
            $table->string('expertise', 120);
            $table->json('tools')->nullable();
            $table->string('cv_path');
            $table->string('cv_name');
            $table->timestamp('consented_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linguist_applications');
    }
};
