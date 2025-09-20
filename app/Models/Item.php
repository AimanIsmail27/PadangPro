<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'item';

    // Primary key
    protected $primaryKey = 'itemID';

    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'itemID',
        'item_Name',
        'item_Quantity',
        'item_Price',
        'item_Decsription',
        'item_Status',
        'staffID'
    ];
}
