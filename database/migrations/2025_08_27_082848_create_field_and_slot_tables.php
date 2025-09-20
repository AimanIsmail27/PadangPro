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
        // Field table
        Schema::create('field', function (Blueprint $table) {
            $table->string('fieldID', 20)->primary();
            $table->string('field_Label', 10);
            $table->string('field_Size', 30);
            $table->string('field_GrassType', 20);
        });

        // Slot table
        Schema::create('slot', function (Blueprint $table) {
            $table->string('slotID', 20)->primary();
            $table->date('slot_Date');
            $table->dateTime('slot_Time');
            $table->string('slot_Status', 20);
            $table->integer('slot_Price');
            $table->string('fieldID', 20)->nullable();

            $table->foreign('fieldID')
                ->references('fieldID')
                ->on('field')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot');
        Schema::dropIfExists('field');
    }
};
