<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental', function (Blueprint $table) {
            $table->date('rental_StartDate')->after('rental_Status');
            $table->date('rental_EndDate')->after('rental_StartDate');
        });
    }

    public function down(): void
    {
        Schema::table('rental', function (Blueprint $table) {
            $table->dropColumn(['rental_StartDate', 'rental_EndDate']);
        });
    }
};
