<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quote form fields from the content document (Contact page).
     */
    public function up(): void
    {
        Schema::table('project_enquiries', function (Blueprint $table) {
            $table->string('phone', 40)->nullable()->after('email');
            $table->string('industry', 120)->nullable()->after('service');
            $table->json('languages')->nullable()->after('industry');
            $table->string('other_language', 120)->nullable()->after('languages');
            $table->string('volume', 160)->nullable()->after('other_language');
            $table->string('deadline', 120)->nullable()->after('volume');
            $table->string('attachment_path')->nullable()->after('message');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->timestamp('consented_at')->nullable()->after('attachment_name');
        });
    }

    public function down(): void
    {
        Schema::table('project_enquiries', function (Blueprint $table) {
            $table->dropColumn(['phone', 'industry', 'languages', 'other_language', 'volume', 'deadline', 'attachment_path', 'attachment_name', 'consented_at']);
        });
    }
};
