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
        Schema::table('item', function (Blueprint $table) {
            // Rename column to correct spelling
            if (Schema::hasColumn('item', 'item_Decsription')) {
                $table->renameColumn('item_Decsription', 'item_Description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item', function (Blueprint $table) {
            // Rollback: rename it back to the wrong spelling
            if (Schema::hasColumn('item', 'item_Description')) {
                $table->renameColumn('item_Description', 'item_Decsription');
            }
        });
    }     
};
