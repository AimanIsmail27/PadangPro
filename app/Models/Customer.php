<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'customer';

    // Primary key
    protected $primaryKey = 'customerID';

    // Disable timestamps since your table does not have created_at & updated_at
    public $timestamps = false;

      // Add this because your primary key is not auto-incrementing
    public $incrementing = false;

    // And also add this because it's a string
    protected $keyType = 'string';

    protected $casts = [
    'customer_Availability' => 'array',
];


    // Columns that can be mass assigned
    protected $fillable = [
        'customerID',
        'customer_FullName',
        'customer_Age',
        'customer_PhoneNumber',
        'customer_Address',
        'customer_Position',
        'customer_SkillLevel',
        'customer_Availability',
        'userID'
    ];

    public function applications()
{
    return $this->hasMany(Applications::class, 'customerID', 'customerID');
}

public function ratings()
{
    return $this->hasMany(Rating::class, 'userID', 'userID');
}

public function user()
    {
        // A Customer profile belongs to a User.
        // The foreign key is 'userID' on the 'customer' table.
        // The primary key is 'userID' on the 'user' table.
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

}
