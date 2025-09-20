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
        // User table
        Schema::create('user', function (Blueprint $table) {
            $table->string('userID', 20)->primary();
            $table->string('user_Email', 50);
            $table->string('user_Password', 20);
            $table->string('user_Type', 20);
        });

        // Customer table
        Schema::create('customer', function (Blueprint $table) {
            $table->string('customerID', 20)->primary();
            $table->string('customer_FullName', 50);
            $table->integer('customer_Age');
            $table->string('customer_PhoneNumber', 20);
            $table->string('customer_Address', 50);
            $table->string('customer_Position', 20);
            $table->string('userID', 20)->nullable();

            $table->foreign('userID')
                ->references('userID')
                ->on('user')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Administrator table
        Schema::create('administrator', function (Blueprint $table) {
            $table->string('adminID', 20)->primary();
            $table->string('admin_FullName', 50);
            $table->integer('admin_Age');
            $table->string('admin_PhoneNumber', 20);
            $table->string('admin_Address', 50);
            $table->string('userID', 20)->nullable();

            $table->foreign('userID')
                ->references('userID')
                ->on('user')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Staff table
        Schema::create('staff', function (Blueprint $table) {
            $table->string('staffID', 20)->primary();
            $table->string('staff_FullName', 50);
            $table->integer('staff_Age');
            $table->string('staff_PhoneNumber', 20);
            $table->string('staff_Address', 50);
            $table->string('staff_Job', 20);
            $table->string('userID', 20)->nullable();

            $table->foreign('userID')
                ->references('userID')
                ->on('user')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
        Schema::dropIfExists('administrator');
        Schema::dropIfExists('customer');
        Schema::dropIfExists('user');
    }
};
