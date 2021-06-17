<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;

class Employee extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use SoftDeletes, Authenticatable, Authorizable, HasFactory;

    protected $table = "employee";

    protected $fillable = [
        'uuid',
        'birthdate',
        'name',
        'phone_number',
        'email',
        'salary',
        'password',
        'user_id',
        'company_id',
        'department_id',
        'position_id',
        'otp_request_token',
        'profile_picture',
        'profile_picture_old'
    ];

    protected $hidden = [
        'company',
        'department_id',
        'position_id',
        'company_id',
        'deleted_at',
        'updated_at',
        'created_at',
        'uuid',
        'password',
        'phone_number_last',
        'user_id',
        'is_resigned',
        'employee_identifier_old',
        'resigned_at',
        'employeeRequests',
        'phone_number',
        'country_iso_code',
        'country_iso_code_last',
        "otp_request_token",
        "remember_token",
        "reset_token",
        "profile_picture_old",
        "pin_code",
        "notifications",
        "lastContact",
        "bank_data_id",
        "beneficiary_name",
        "account_number",
    ];

    protected $casts = [
        'is_resigned' => 'boolean',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function employeeRequests()
    {
        return $this->hasMany(EmployeeRequest::class, "employee_uuid", "uuid");
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasSameCompanyIdentity()
    {
        return auth()->user()->company->id === $this->company->id;
    }

    public function hasBeenResigned()
    {
        return $this->is_resigned;
    }

    public function otpCode()
    {
        return $this->hasOne(OtpCode::class, "employee_uuid", "uuid");
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function lastContact()
    {
        return $this->hasOne(LastContact::class, 'employee_uuid', 'uuid');
    }
}
