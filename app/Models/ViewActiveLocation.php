<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 */
class ViewActiveLocation extends Model
{
    protected $table = 'view_active_location';

    public $timestamps = false;

    protected $fillable = [
        'province',
        'city',
        'district',
    ];

    protected $guarded = [];


}
