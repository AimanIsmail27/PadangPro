<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'staff';

    // Primary key
    protected $primaryKey = 'staffID';

    // Disable timestamps
    public $timestamps = false;

    // Treat primary key as string and not auto-incrementing
    protected $keyType = 'string';
    public $incrementing = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'staffID',
        'staff_FullName',
        'staff_Age',
        'staff_PhoneNumber',
        'staff_Address',
        'staff_Job',
        'userID'
    ];

    // Relationship: Staff -> Items
    public function items()
    {
        return $this->hasMany(Item::class, 'staffID', 'staffID');
    }

    // Relationship: Staff -> User
    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
}
