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

    // Columns that can be mass assigned
    protected $fillable = [
        'customerID',
        'customer_FullName',
        'customer_Age',
        'customer_PhoneNumber',
        'customer_Address',
        'customer_Position',
        'userID'
    ];
}
