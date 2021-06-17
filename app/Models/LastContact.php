<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LastContact extends Model
{
    use SoftDeletes;

    protected $table = "last_contact";

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'employee_uuid',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_uuid', 'uuid');
    }
}
