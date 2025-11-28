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
        Schema::table('rating', function (Blueprint $table) {
            // Add nullable columns for booking and rental
            // We use string(20) because your IDs are like 'BOOK690...'
            $table->string('bookingID', 20)->nullable()->after('userID');
            $table->string('rentalID', 20)->nullable()->after('bookingID');

            // Optional: Add indexes for faster searching
            $table->index('bookingID');
            $table->index('rentalID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rating', function (Blueprint $table) {
            // Drop the columns if we rollback
            $table->dropColumn(['bookingID', 'rentalID']);
        });
    }
};