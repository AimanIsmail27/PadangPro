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
    Schema::table('customer', function (Blueprint $table) {
        $table->string('customer_Image')->nullable()->after('customer_Address');
    });
}

public function down(): void
{
    Schema::table('customer', function (Blueprint $table) {
        $table->dropColumn('customer_Image');
    });
}
};
