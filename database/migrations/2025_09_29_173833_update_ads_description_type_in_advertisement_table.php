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
            // Change ads_Description to TEXT to allow longer content
            $table->text('ads_Description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            // Revert back to VARCHAR(255)
            $table->string('ads_Description', 255)->change();
        });
    }
};
