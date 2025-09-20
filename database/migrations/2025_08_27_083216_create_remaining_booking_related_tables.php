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
        // Booking Table
        Schema::create('booking', function (Blueprint $table) {
            $table->string('bookingID', 20)->primary();
            $table->string('booking_Name', 50);
            $table->string('booking_Email', 50);
            $table->string('booking_PhoneNumber', 20);
            $table->string('booking_BackupNumber', 20);
            $table->string('booking_Status', 20);
            $table->string('fieldID', 20)->nullable();
            $table->string('slotID', 20)->nullable();
            $table->string('userID', 20)->nullable();

            $table->foreign('fieldID')->references('fieldID')->on('field')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('slotID')->references('slotID')->on('slot')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('userID')->references('userID')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });

        // Item Table
        Schema::create('item', function (Blueprint $table) {
            $table->string('itemID', 20)->primary();
            $table->string('item_Name', 20);
            $table->integer('item_Quantity');
            $table->integer('item_Price');
            $table->string('item_Decsription', 30);
            $table->string('item_Status', 20);
            $table->string('staffID', 20)->nullable();

            $table->foreign('staffID')->references('staffID')->on('staff')->onDelete('cascade')->onUpdate('cascade');
        });

        // Rental Table
        Schema::create('rental', function (Blueprint $table) {
            $table->string('rentalID', 20)->primary();
            $table->string('rental_Name', 50);
            $table->string('rental_Email', 50);
            $table->string('rental_PhoneNumber', 20);
            $table->string('rental_BackupNumber', 20);
            $table->string('rental_Status', 20);
            $table->string('itemID', 20)->nullable();
            $table->string('userID', 20)->nullable();

            $table->foreign('itemID')->references('itemID')->on('item')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('userID')->references('userID')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });

        // Payment Table
        Schema::create('payment', function (Blueprint $table) {
            $table->string('paymentID', 20)->primary();
            $table->string('payer_Name', 50);
            $table->string('payer_BankAccount', 20);
            $table->integer('payment_Amount');
            $table->string('payment_Status', 20);
            $table->string('bookingID', 20)->nullable();
            $table->string('rentalID', 20)->nullable();
            $table->string('userID', 20)->nullable();

            $table->foreign('bookingID')->references('bookingID')->on('booking')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rentalID')->references('rentalID')->on('rental')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('userID')->references('userID')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });

        // Advertisement Table
        Schema::create('advertisement', function (Blueprint $table) {
            $table->string('adsID', 20)->primary();
            $table->string('ads_Name', 20);
            $table->string('ads_Type', 30);
            $table->integer('ads_Price');
            $table->string('ads_Decsription', 30);
            $table->string('ads_Status', 20);
            $table->dateTime('ads_SlotTime');
            $table->string('customerID', 20)->nullable();

            $table->foreign('customerID')->references('customerID')->on('customer')->onDelete('cascade')->onUpdate('cascade');
        });

        // Rating Table
        Schema::create('rating', function (Blueprint $table) {
            $table->string('ratingID', 20)->primary();
            $table->integer('rating_Score');
            $table->string('review_Given', 30);
            $table->date('review_Date');
            $table->dateTime('review_Time');
            $table->string('userID', 20)->nullable();

            $table->foreign('userID')->references('userID')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });

        // Report Table
        Schema::create('report', function (Blueprint $table) {
            $table->string('reportID', 20)->primary();
            $table->string('report_Title', 50);
            $table->string('report_Information', 100);
            $table->string('rentalID', 20)->nullable();
            $table->string('bookingID', 20)->nullable();
            $table->string('userID', 20)->nullable();

            $table->foreign('rentalID')->references('rentalID')->on('rental')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('bookingID')->references('bookingID')->on('booking')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('userID')->references('userID')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report');
        Schema::dropIfExists('rating');
        Schema::dropIfExists('advertisement');
        Schema::dropIfExists('payment');
        Schema::dropIfExists('rental');
        Schema::dropIfExists('item');
        Schema::dropIfExists('booking');
    }
};
