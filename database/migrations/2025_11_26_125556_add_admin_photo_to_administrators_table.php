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
    Schema::table('administrator', function (Blueprint $table) {
        $table->string('admin_Photo')->nullable()->after('admin_Address');
    });
}

public function down(): void
{
    Schema::table('administrator', function (Blueprint $table) {
        $table->dropColumn('admin_Photo');
    });
}

};
