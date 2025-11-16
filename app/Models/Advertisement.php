<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'advertisement';

    // Primary key
    protected $primaryKey = 'adsID';

    // Disable timestamps
    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'adsID',
        'ads_Name',
        'ads_Type',
        'ads_Price',
        'ads_Description',
        'ads_Status',
        'ads_SlotTime',
        'ads_RequiredPosition',
        'ads_TargetSkillLevel',
        'ads_MatchIntensity',
        'ads_MaxPlayers',
        'customerID'
    ];

    

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');
    }

    /**
     * Get all applications for this ad.
     */
    public function applications()
    {
        return $this->hasMany(Applications::class, 'adsID', 'adsID');
    }
}
