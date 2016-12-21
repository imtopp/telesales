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
      
    ];

    protected $guarded = [];


}
