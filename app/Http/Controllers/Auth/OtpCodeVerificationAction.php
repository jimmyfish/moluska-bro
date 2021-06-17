<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtpCodeVerificationAction extends Controller
{
    public function verify(Request $request)
    {
        $this->validate($request, [
            'requestToken' => 'required|string|min:128|max:128',
            'otpCode' => 'required|numeric|min:100000|max:999999'
        ]);

        $employee = Employee::where('otp_request_token', $request->get('requestToken'))->first();

        if (!$employee) return $this->resourceNotFound();

        if ($employee->hasBeenResigned()) return $this->resourceNotFound();

        if (!$employee->otpCode) return $this->resourceNotFound();

        if ((int) $employee->otpCode->code !== (int) $request->get('otpCode')) {
            $attempt = $employee->otpCode->attempt + 1;

            if ($attempt >= 3) $this->destroyToken($employee);

            $employee->otpCode()->update(['attempt' => $attempt]);

            return response()->json([
                'meta' => [
                    'status' => "Bad request",
                    'code' => 400,
                    'message' => 'OTP Code missmatch'
                ],
            ]);
        }

        $token = Auth::guard('api-employee')->login($employee);

        $this->destroyToken($employee);

        return response()->json([
            'meta' => [
                'code'   => 200,
                'status' => 'success'
            ],
            'data' => [
                'token'      => $token,
                'tokenType'  => 'bearer',
                'expires_in' => config('jwt.ttl'),
            ]
        ]);
    }

    private function destroyToken(Employee $employee): void
    {
        DB::beginTransaction();
        try {
            Employee::find($employee->id)->update([
                'otp_request_token' => null
            ]);

            $employee->otpCode->delete();
            DB::commit();
        } catch (\Exception $e) {
            Log::warning('Process failing at : ' . $e->getMessage());
            DB::rollBack();
        }
    }
}
