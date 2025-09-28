<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item', function (Blueprint $table) {
            // Change description from VARCHAR(30) to VARCHAR(255)
            $table->string('item_Description', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('item', function (Blueprint $table) {
            // Rollback to original VARCHAR(30)
            $table->string('item_Description', 30)->change();
        });
    }
};
