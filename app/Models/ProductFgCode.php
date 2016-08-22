<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductFgCode
 */
class ProductFgCode extends Model
{
    protected $table = 'product_fg_code';

    public $timestamps = false;

    protected $fillable = [
        'fg_code',
        'product_colour_id',
        'price',
        'description',
        'image_url',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

    public function productColour(){
		return $this->belongsTo('App\Models\ProductColour');
	}
}