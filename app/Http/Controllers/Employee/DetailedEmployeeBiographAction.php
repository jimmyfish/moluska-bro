<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Enum\EmployeeRequestEnum;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;

class DetailedEmployeeBiographAction extends Controller
{
    private $localizePhoneNumberTransformator;

    public function __construct(
        LocalizePhoneNumberTransformator $localizePhoneNumberTransformator
    ) {
        $this->localizePhoneNumberTransformator = $localizePhoneNumberTransformator;
    }

    public function index(Request $request)
    {
        /**
         * @var $employee Employee
         */
        $employee = Employee::find($request->id);

        if (!$employee) return $this->resourceNotFound();

        if (!$employee->hasSameCompanyIdentity()) return $this->unauthorizedMessage();

        $employee->department;
        $employee->position;
        $employee->countryCode = 'ID';
        $employee->isResigned = $employee->is_resigned;
        $employee->resignedAt = $employee->resigned_at;
        $employee->beneficiaryName = $employee->beneficiary_name;
        $employee->accountNumber = $employee->account_number;
        $employee->bankId = $employee->bank_data_id;
        $employee->profile_picture = $employee->profile_picture ?
            config('app.url') . config('app.imageURI') . $employee->profile_picture :
            null;

        $employee->phoneNumber = $this->localizePhoneNumberTransformator->temporarilyRevertBack(
            $employee->phone_number,
            $employee->country_iso_code ?? "ID"
        );

        /**
         * @var $historicalData EmployeeRequest
         */
        $historicalData = $employee->employeeRequests()->orderBy('created_at', 'DESC')->get();

        $historicalData->each(function ($data) {
            $data->createdAt = $data->created_at;
            $data->statusString = EmployeeRequestEnum::STATUS[$data->status];
            $data->approvalDate = $data->approved_at;
            $data->bankId = $data->bank_data_id;
        });

        return response()->json([
            'meta' => [
                'profile' => $employee
            ],
            'data' => $historicalData
        ]);
    }
}
