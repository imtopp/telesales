<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 */
class Transaction extends Model
{
    protected $table = 'transaction';

    public $timestamps = false;

    protected $fillable = [
        'customer_name',
        'customer_address',
        'customer_identity_type',
        'customer_identity_number',
        'customer_email',
        'customer_mdn',
        'customer_location_province',
        'customer_location_city',
        'customer_location_district',
        'customer_delivery_address',
        'product_category',
        'product_name',
        'product_colour',
        'product_fg_code',
        'payment_method',
        'courier',
        'courier_package',
        'delivery_price',
        'refference_number',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
