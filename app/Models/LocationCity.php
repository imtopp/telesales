<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LocationCity
 */
class LocationCity extends Model
{
    protected $table = 'location_city';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'province_id',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

    public function province(){
        return $this->belongsTo('App\Models\LocationProvince','province_id');
    }

    public function district(){
      return $this->hasMany('App\Models\LocationDistrict','city_id');
    }
}
