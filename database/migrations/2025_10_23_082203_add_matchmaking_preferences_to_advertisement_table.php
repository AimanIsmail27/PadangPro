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
        Schema::table('advertisement', function (Blueprint $table) {
            // For 'Opponent Search': What skill level are they looking for?
            $table->integer('ads_TargetSkillLevel')->nullable()->after('ads_MaxPlayers');
            
            // For 'Opponent Search': Are they serious or just for fun?
            $table->string('ads_MatchIntensity', 20)->nullable()->after('ads_TargetSkillLevel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->dropColumn(['ads_TargetSkillLevel', 'ads_MatchIntensity']);
        });
    }
};