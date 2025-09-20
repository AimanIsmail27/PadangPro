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
            $table->integer('customer_Age')->nullable()->change();
            $table->string('customer_PhoneNumber', 20)->nullable()->change();
            $table->string('customer_Address', 50)->nullable()->change();
            $table->string('customer_Position', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->integer('customer_Age')->nullable(false)->change();
            $table->string('customer_PhoneNumber', 20)->nullable(false)->change();
            $table->string('customer_Address', 50)->nullable(false)->change();
            $table->string('customer_Position', 20)->nullable(false)->change();
        });
    }
};
