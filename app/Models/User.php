<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    // Table name in your database
    protected $table = 'user';

    // Primary key column
    protected $primaryKey = 'userID';

    // Your table does not have created_at/updated_at
    public $timestamps = false;

    // Add this because your primary key is not auto-incrementing
    public $incrementing = false;

    // And also add this because it's a string
    protected $keyType = 'string';

    // Columns that can be mass assigned
    protected $fillable = [
        'userID',
        'user_Email',
        'user_Password',
        'user_Type'
    ];

     public function staff()
    {
        return $this->hasOne(Staff::class, 'userID', 'userID');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'userID', 'userID');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'userID', 'userID');
    }
}
