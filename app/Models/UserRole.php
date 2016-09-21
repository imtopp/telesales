<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRole
 */
class UserRole extends Model
{
    protected $table = 'user_roles';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];

    public function user(){
      return $this->hasMany('App\Models\User','role_id');
    }
}
