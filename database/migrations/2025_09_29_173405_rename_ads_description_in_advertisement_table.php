<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            // Rename column from ads_Decsription to ads_Description
            $table->renameColumn('ads_Decsription', 'ads_Description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            // Revert column name back
            $table->renameColumn('ads_Description', 'ads_Decsription');
        });
    }
};
