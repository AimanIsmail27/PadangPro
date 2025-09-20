<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'payment';

    // Primary key
    protected $primaryKey = 'paymentID';

    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'paymentID',
        'payer_Name',
        'payer_BankAccount',
        'payment_Amount',
        'payment_Status',
        'bookingID',
        'rentalID',
        'userID'
    ];
}
