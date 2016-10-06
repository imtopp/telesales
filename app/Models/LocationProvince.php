<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LocationProvince
 */
class LocationProvince extends Model
{
    protected $table = 'location_province';

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

    public function city(){
      return $this->hasMany('App\Models\LocationCity','province_id');
    }
}
