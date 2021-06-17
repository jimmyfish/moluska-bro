<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $table = 'department';

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'department_id',
        'company_id',
    ];

    // PARENT RELATIONS

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    // CHILD RELATIONS

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employeeRequests()
    {
        return $this->hasMany(EmployeeRequest::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
