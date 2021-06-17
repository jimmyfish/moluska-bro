<?php

namespace App\Http\Controllers\Auth\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class ResetPinAction extends Controller
{
    public function __invoke(Request $request)
    {
        $newPinUnsecure = rand(100000, 999999);
        $newPinSecure = hash('sha256', $newPinUnsecure);

        /** @var Employee $user */
        $user = Auth::guard('api-employee')->user();

        $user->pin_code = $newPinSecure;

        $user->save();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
                'message' => "PIN Changed to $newPinUnsecure. This message will be suppressed in the future update."
            ],
        ]);
    }
}
