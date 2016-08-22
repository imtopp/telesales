<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerInfo
 */
class CustomerInfo extends Model
{
    protected $table = 'customer_info';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'identity_type',
        'identity_number',
        'email',
        'mdn',
        'delivery_address',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}