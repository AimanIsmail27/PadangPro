<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Insert into user table
        DB::table('user')->insert([
            'userID'       => 'U4ZDZOXDG',  // change if you want it auto-generated
            'user_Email'   => 'admin111@gmail.com',
            'user_Password'=> Hash::make('admin123'), // default admin password
            'user_Type'    => 'administrator',
        ]);

        // Insert related admin details
        DB::table('administrator')->insert([
            'adminID'          => 'ADM0005',  // change if your system auto-generates
            'admin_FullName'   => 'new one',
            'admin_Age'        => 23,
            'admin_PhoneNumber'=> '0194399920',
            'admin_Address'    => 'NO 25, LORONG MAHKOTA IMPIAN 2/30',
            'admin_Photo'      => null,
            'userID'           => 'U4ZDZOXDG',
        ]);
    }

    public function down(): void
    {
        DB::table('administrator')->where('adminID', 'ADM0005')->delete();
        DB::table('user')->where('userID', 'U4ZDZOXDG')->delete();
    }
};
