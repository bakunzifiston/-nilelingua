<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets the dashboard mark website submissions as new or read.
     */
    public function up(): void
    {
        foreach (['project_enquiries', 'linguist_applications'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['project_enquiries', 'linguist_applications'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }
    }
};
