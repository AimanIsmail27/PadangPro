<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'report';

    /**
     * The primary key for the model.
     * @var string
     */
    protected $primaryKey = 'reportID';

    /**
     * Indicates if the model's ID is auto-incrementing.
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * These now match your new table structure.
     * @var array
     */
    protected $fillable = [
        'reportID',
        'report_Title',
        'report_type',
        'parameters',
        'published_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     * This tells Laravel to automatically handle the 'parameters' JSON column.
     * @var array
     */
    protected $casts = [
        'parameters' => 'array',
    ];

    /**
     * Get the user who published this report.
     */
    public function publisher()
    {
        // A Report belongs to a User who published it.
        return $this->belongsTo(User::class, 'published_by_user_id', 'userID');
    }
}