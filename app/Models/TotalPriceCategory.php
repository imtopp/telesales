<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TotalPriceCategory
 */
class TotalPriceCategory extends Model
{
    protected $table = 'total_price_category';

    public $timestamps = false;

    protected $fillable = [
        'min_price',
        'max_price',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}