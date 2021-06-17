<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatchDeviceTokenAction extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->get('deviceToken')) {
            return $this->unauthorizedMessage();
        }

        $user = Auth::guard('api-employee')->user();
        
        $device = new Device([
            'employee_uuid' => $user->uuid,
            'device_token' => $request->get('deviceToken'),
            'hash' => base64_encode(
                json_encode([
                    'employeeId' => $user->uuid,
                    'deviceToken' => $request->get('deviceToken')
                ])
            )
        ]);

        DB::beginTransaction();

        try {
            $device->save();

            DB::commit();
            return $this->processSucceed();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalError($e);
        }
    }
}
