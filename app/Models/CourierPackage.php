<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCategory
 */
class CourierPackage extends Model
{
    protected $table = 'courier_package';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
        'courier_id',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
