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
        'prefix_id',
        'customer_info_id',
        'product_fg_code_id',
        'qty',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}