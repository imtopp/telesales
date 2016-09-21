<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Configuration
 */
class Configuration extends Model
{
    protected $table = 'configuration';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}