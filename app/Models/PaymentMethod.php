<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethod
 */
class PaymentMethod extends Model
{
    protected $table = 'payment_method';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'redirect_url',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}