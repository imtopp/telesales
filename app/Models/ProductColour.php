<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductColour
 */
class ProductColour extends Model
{
    protected $table = 'product_colour';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'product_id',
        'image_url',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}