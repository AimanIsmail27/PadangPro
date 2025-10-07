<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    use HasFactory;

    // Explicit table name (if pluralization doesn’t match convention)
    protected $table = 'applications';

    // Primary key
    protected $primaryKey = 'applicationID';
    public $incrementing = false; // since you use varchar(20) instead of auto-increment
    protected $keyType = 'string';

    // Mass assignable attributes
    protected $fillable = [
        'applicationID',
        'adsID',
        'customerID',
        'status',
        'note',
        'match_score',
    ];

    // Relationships
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'adsID', 'adsID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');
    }
}
