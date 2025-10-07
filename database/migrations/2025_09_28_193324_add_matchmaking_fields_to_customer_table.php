<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            // Add skill level (1–5 rating system)
            $table->unsignedTinyInteger('customer_SkillLevel')->nullable()->after('customer_Position');

            // Add availability (JSON or text, store preferred days/times)
            $table->json('customer_Availability')->nullable()->after('customer_SkillLevel');
        });
    }

    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn(['customer_SkillLevel', 'customer_Availability']);
        });
    }
};
