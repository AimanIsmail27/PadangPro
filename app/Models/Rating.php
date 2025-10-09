<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'rating';

    // Primary key
    protected $primaryKey = 'ratingID';

    // Disable timestamps
    public $timestamps = false;

    public $incrementing = false; // since we generate manually
    protected $keyType = 'string';

    // Columns that can be mass assigned
    protected $fillable = [
        'ratingID',
        'rating_Score',
        'review_Given',
        'review_Date',
        'review_Time',
        'userID'
    ];

    public function customer()
{
    return $this->belongsTo(Customer::class, 'userID', 'userID');
}

}
