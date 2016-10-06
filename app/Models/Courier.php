<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCategory
 */
class Courier extends Model
{
    protected $table = 'courier';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
