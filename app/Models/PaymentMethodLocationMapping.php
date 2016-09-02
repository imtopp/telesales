<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethodLocationMapping
 */
class PaymentMethodLocationMapping extends Model
{
    protected $table = 'payment_method_location_mapping';

    public $timestamps = false;

    protected $fillable = [
        'location_district_id',
        'payment_method_id',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}