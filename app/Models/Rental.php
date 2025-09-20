<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'rental';

    // Primary key
    protected $primaryKey = 'rentalID';

    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'rentalID',
        'rental_Name',
        'rental_Email',
        'rental_PhoneNumber',
        'rental_BackupNumber',
        'rental_Status',
        'itemID',
        'userID'
    ];
}
