<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            // Add required position for matchmaking
            $table->string('ads_RequiredPosition', 30)->nullable()->after('ads_Status');

            // Add max players needed for this ad
            $table->unsignedInteger('ads_MaxPlayers')->nullable()->after('ads_RequiredPosition');
        });
    }

    public function down(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->dropColumn(['ads_RequiredPosition', 'ads_MaxPlayers']);
        });
    }
};
