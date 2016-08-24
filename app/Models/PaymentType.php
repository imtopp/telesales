<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentType
 */
class PaymentType extends Model
{
    protected $table = 'payment_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'redirect_url',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}