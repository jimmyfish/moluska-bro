<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = "device";

    protected $fillable = [
        'employee_uuid',
        'device_token',
        'hash'
    ];

    protected $guards = [
        'id'
    ];
}
