<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryPrice
 */
class CourierInternalDeliveryPrice extends Model
{
    protected $table = 'courier_internal_delivery_price';

    public $timestamps = false;

    protected $fillable = [
        'courier_location_maping_id',
        'price',
        'input_by',
        'input_date',
        'update_by',
        'update_date'
    ];

    protected $guarded = [];


}