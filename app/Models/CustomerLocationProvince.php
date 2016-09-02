<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerLocationProvince
 */
class CustomerLocationProvince extends Model
{
    protected $table = 'customer_location_province';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}