<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rating', function (Blueprint $table) {
            // Change 'review' column to TEXT type (supports longer text)
            $table->text('review_Given')->change();
        });
    }

    public function down(): void
    {
        Schema::table('rating', function (Blueprint $table) {
            // Revert it back to string if needed
            $table->string('review_Given', 255)->change();
        });
    }
};
