<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethodLocationMapping
 */
class CourierLocationMapping extends Model
{
    protected $table = 'courier_location_mapping';

    public $timestamps = false;

    protected $fillable = [
        'courier_id',
        'location_district_id',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
