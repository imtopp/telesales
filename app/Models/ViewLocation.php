<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 */
class ViewLocation extends Model
{
    protected $table = 'view_location';

    public $timestamps = false;

    protected $fillable = [
        'province',
        'city',
        'district',
    ];

    protected $guarded = [];


}
