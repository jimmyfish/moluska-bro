<?php

namespace App\Http\Controllers\Auth\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyPinAction extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'pin' => 'required|numeric|min:100000|max:999999'
        ]);

        /** @var Employee $user */
        $user = Auth::guard('api-employee')->user();
        $pinSecure = hash('sha256', $request->get('pin'));

        if ($user->pin_code !== $pinSecure) {
            return $this->unauthorizedMessage();
        }

        return $this->processSucceed();
    }
}
