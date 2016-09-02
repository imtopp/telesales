<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerLocationCity
 */
class CustomerLocationCity extends Model
{
    protected $table = 'customer_location_city';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'province_id',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}