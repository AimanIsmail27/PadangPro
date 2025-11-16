<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report', function (Blueprint $table) {
            // 1. Drop the foreign key constraints FIRST.
            // Laravel's convention is tableName_columnName_foreign
            $table->dropForeign(['rentalID']);
            $table->dropForeign(['bookingID']);
            $table->dropForeign(['userID']);

            // 2. Now it's safe to drop the columns.
            $table->dropColumn(['report_Information', 'rentalID', 'bookingID']);

            // 3. Add the new columns.
            $table->string('report_type', 50)->after('report_Title');
            $table->json('parameters')->after('report_type');

            // 4. Rename the userID column.
            $table->renameColumn('userID', 'published_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report', function (Blueprint $table) {
            // This does the reverse of the 'up' method to allow for rollbacks.
            $table->renameColumn('published_by_user_id', 'userID');
            $table->dropColumn(['report_type', 'parameters']);
            $table->string('report_Information', 100);
            $table->string('rentalID', 20)->nullable();
            $table->string('bookingID', 20)->nullable();
            
            // Re-add the foreign key constraints
            $table->foreign('rentalID')->references('rentalID')->on('rental');
            $table->foreign('bookingID')->references('bookingID')->on('booking');
            $table->foreign('userID')->references('userID')->on('user');
        });
    }
};