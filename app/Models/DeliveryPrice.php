<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryPrice
 */
class DeliveryPrice extends Model
{
    protected $table = 'delivery_price';

    public $timestamps = false;

    protected $fillable = [
        'payment_method_location_maping_id',
        'total_price_category_id',
        'price',
        'input_by',
        'input_date',
        'update_by',
        'update_date'
    ];

    protected $guarded = [];

        
}