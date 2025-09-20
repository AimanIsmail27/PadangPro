<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'administrator';

    // Primary key
    protected $primaryKey = 'adminID';

    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'adminID',
        'admin_FullName',
        'admin_Age',
        'admin_PhoneNumber',
        'admin_Address',
        'userID'
    ];
}
