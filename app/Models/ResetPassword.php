<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRole
 */
class ResetPassword extends Model
{
    protected $table = 'reset_password';

    public $timestamps = false;

    protected $fillable = [
        'user_email',
        'token',
        'status',
        'input_date',
        'input_by',
        'update_date',
        'update_by'
    ];

    protected $guarded = [];
}
