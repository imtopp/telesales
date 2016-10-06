<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LocationDistrict
 */
class LocationDistrict extends Model
{
    protected $table = 'location_district';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'city_id',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

    public function city(){
        return $this->belongsTo('App\Models\LocationCity','city_id');
    }
}
