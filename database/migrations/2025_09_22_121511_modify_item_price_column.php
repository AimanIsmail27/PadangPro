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
    Schema::table('item', function (Blueprint $table) {
        $table->decimal('item_Price', 10, 2)->change();
    });
}

public function down()
{
    Schema::table('item', function (Blueprint $table) {
        $table->integer('item_Price')->change();
    });
}

};
