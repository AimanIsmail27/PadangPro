<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'slot';

    // Primary key
    protected $primaryKey = 'slotID';

    // Primary key is string, not auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Disable timestamps
    public $timestamps = false;

    // Mass assignable
    protected $fillable = [
        'slotID',
        'slot_Date',
        'slot_Time',
        'slot_Status',
        'slot_Price',
        'fieldID'
    ];

    // Define relationship
    public function field()
    {
        return $this->belongsTo(Field::class, 'fieldID', 'fieldID');
    }
}
