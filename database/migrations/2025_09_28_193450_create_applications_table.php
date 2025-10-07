<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->string('applicationID', 20)->primary(); // Custom ID, or use ->id() for auto increment
            $table->string('adsID', 20);   // FK to advertisement
            $table->string('customerID', 20); // FK to customer

            // Application status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Optional: Store AI score (recommendation/matching)
            $table->unsignedTinyInteger('match_score')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('adsID')->references('adsID')->on('advertisement')->onDelete('cascade');
            $table->foreign('customerID')->references('customerID')->on('customer')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
