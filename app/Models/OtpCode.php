<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $table = 'otp_code';

    protected $fillable = [
        'employee_uuid',
        'code'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'code' => 'int',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, "employee_uuid", "uuid");
    }
}
