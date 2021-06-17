<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\Employee;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class OtpLoginAction extends Controller
{
    private $transformator;

    public function __construct(LocalizePhoneNumberTransformator $transformator)
    {
        $this->transformator = $transformator;
    }

    public function do(Request $request)
    {
        $this->validate($request, [
            'phoneNumber' => 'required|string',
            'localization' => 'string|min:1|max:3'
        ]);

        if (!is_numeric(str_replace('+', '', $request->get('phoneNumber')))) {
            return $this->illegalBody();
        }

        $phoneNumberISO = $this->transformator->transform($request->get('phoneNumber'), $request->get('localization'));

        $employee = Employee::where('phone_number', $phoneNumberISO)->first();

        if (!$employee) return $this->resourceNotFound();

        if ($employee->hasBeenResigned()) return $this->resourceNotFound();

        // ADD FUNCTION TO SEND MESSAGE HERE
        $otpCode = rand(100000, 999999);

        $otpCodeData = new OtpCode([
            'code' => $otpCode,
            'employee_uuid' => $employee->uuid
        ]);

        $requestToken = hash('sha512', uniqid());

        DB::beginTransaction();
        try {
            $employeeData = Employee::find($employee->id)->update([
                'otp_request_token' => $requestToken
            ]);

            if ($employee->otpCode) $employee->otpCode->delete();
            $otpCodeData->save();

            DB::commit();
        } catch (\Exception $e) {
            Log::warning('Inserting OTP Code failing at : ' . $e->getMessage());
            DB::rollBack();
        }

        return response()->json([
            'meta' => [
                'status' => 'success',
                'code' => 200,
                'message' => 'OTP Code was sent'
            ],
            'data' => [
                'requestToken' => $requestToken,
            ]
        ]);
    }
}
