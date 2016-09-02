<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerLocationDistrict
 */
class CustomerLocationDistrict extends Model
{
    protected $table = 'customer_location_district';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'city_id',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}