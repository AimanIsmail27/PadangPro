<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    public function administrator()
    {
        return $this->hasOne(Administrator::class, 'userID', 'userID');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->user_Type === 'administrator' && $this->administrator) {
                    return $this->administrator->admin_FullName;
                }
                if ($this->user_Type === 'customer' && $this->customer) {
                    return $this->customer->customer_FullName;
                }
                if ($this->user_Type === 'staff' && $this->staff) {
                    return $this->staff->staff_FullName;
                }
                return 'N/A'; // Fallback
            },
        );
    }

    
}
