<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethodLocationMapping
 */
class CodLocationMapping extends Model
{
    protected $table = 'cod_location_mapping';

    public $timestamps = false;

    protected $fillable = [
        'location_district_id',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];


}
