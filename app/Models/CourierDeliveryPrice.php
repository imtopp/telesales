<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryPrice
 */
class CourierDeliveryPrice extends Model
{
    protected $table = 'courier_delivery_price';

    public $timestamps = false;

    protected $fillable = [
        'courier_location_maping_id',
        'courier_price_category_id',
        'price',
        'input_by',
        'input_date',
        'update_by',
        'update_date'
    ];

    protected $guarded = [];


}
