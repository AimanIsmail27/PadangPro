<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rating', function (Blueprint $table) {
            $table->string('status')->default('normal')->after('review_Time'); 
            $table->string('flag_reason')->nullable()->after('status'); 
            $table->json('admin_action')->nullable()->after('flag_reason'); 
        });
    }

    public function down()
    {
        Schema::table('rating', function (Blueprint $table) {
            $table->dropColumn(['status', 'flag_reason', 'admin_action']);
        });
    }
};
