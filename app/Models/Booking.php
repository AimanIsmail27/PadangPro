<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'booking';

    // Primary key
    protected $primaryKey = 'bookingID';

    public $incrementing = false;
    protected $keyType = 'string';
    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'bookingID',
        'booking_Name',
        'booking_Email',
        'booking_PhoneNumber',
        'booking_BackupNumber',
        'booking_Status',
        'fieldID',
        'slotID',
        'userID'
    ];

     // Relationship to Slot
    public function slot()
    {
        return $this->belongsTo(\App\Models\Slot::class, 'slotID', 'slotID');
    }

    // Relationship to Field
    public function field()
    {
        return $this->belongsTo(\App\Models\Field::class, 'fieldID', 'fieldID');
    }

    // Optional: Relationship to User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'userID', 'userID'); // adjust key if needed
    }

    public function payment()
{
    return $this->hasOne(Payment::class, 'bookingID', 'bookingID');
}



}

