<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'report';

    // Primary key
    protected $primaryKey = 'reportID';

    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'reportID',
        'report_Title',
        'report_Information',
        'rentalID',
        'bookingID',
        'userID'
    ];
}
