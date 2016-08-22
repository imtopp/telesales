<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCategory
 */
class ProductCategory extends Model
{
    protected $table = 'product_category';

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