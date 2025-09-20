<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'field';

    // Primary key
    protected $primaryKey = 'fieldID';

    public $incrementing = false;
    protected $keyType = 'string';

    // Disable timestamps
    public $timestamps = false;

    // Columns that can be mass assigned
    protected $fillable = [
        'fieldID',
        'field_Label',
        'field_Size',
        'field_GrassType'
    ];
}
