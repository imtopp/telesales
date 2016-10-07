<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 */
class TransactionStatus extends Model
{
    protected $table = 'transaction_status';

    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
