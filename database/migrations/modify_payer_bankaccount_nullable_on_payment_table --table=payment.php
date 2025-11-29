<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->string('payer_BankAccount', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->string('payer_BankAccount', 255)->nullable(false)->change();
        });
    }
};
