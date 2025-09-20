<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('customer', function (Blueprint $table) {
        $table->integer('customer_Age')->nullable()->change();
    });
}

public function down()
{
    Schema::table('customer', function (Blueprint $table) {
        $table->integer('customer_Age')->nullable(false)->change();
    });
}

};
