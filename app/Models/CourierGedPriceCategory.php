<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TotalPriceCategory
 */
class CourierGedPriceCategory extends Model
{
    protected $table = 'courier_ged_price_category';

    public $timestamps = false;

    protected $fillable = [
        'min_price',
        'max_price',
        'name',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
