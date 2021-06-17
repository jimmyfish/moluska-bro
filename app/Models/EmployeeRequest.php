<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRequest extends Model
{
    use SoftDeletes;

    protected $table = 'employee_request';

    protected $hidden = [
        'employee_uuid',
        'deleted_at',
        'updated_at',
        'company_id',
        'department_id',
        'approved_at',
    ];

    protected $fillable = [
        'employee_uuid',
        'company_id',
        'department_id',
        'amount',
        'fee',
        'total',
        'account_number',
        'beneficiary_name',
        'self_withdraw',
        'approved_at',
        'bank_data_id',
        'phone_number'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, "employee_uuid", "uuid");
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function bank()
    {
        return $this->belongsTo(BankList::class, "bank_data_id", "bank_id");
    }

    public function hasAbilityToApprove()
    {
        return $this->company_id === auth()->user()->company_id;
    }
}
