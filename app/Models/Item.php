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

    public $incrementing = false; // important for non-integer primary keys
    protected $keyType = 'string';
    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'itemID',
        'item_Name',
        'item_Quantity',
        'item_Price',
        'item_Description',
        'item_Status',
        'staffID'
    ];

    public function staff()
{
    return $this->belongsTo(Staff::class, 'staffID', 'staffID');
}
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'itemID', 'itemID');
    }

}
