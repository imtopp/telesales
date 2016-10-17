<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TotalPriceCategory
 */
class CourierPriceCategory extends Model
{
    protected $table = 'courier_price_category';

    public $timestamps = false;

    protected $fillable = [
        'courier_id',
        'name',
        'min_price',
        'max_price',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
