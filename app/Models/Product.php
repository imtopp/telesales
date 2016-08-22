<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 */
class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

        
}