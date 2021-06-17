<?php

namespace App\Http\Controllers\Auth\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class ChangePinAction extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'oldPin' => 'required|numeric|min:100000|max:999999',
            'pin' => 'required|numeric|min:100000|max:999999',
            'confirmPin' => 'required|numeric|min:100000|max:999999|same:pin'
        ]);

        $pinSecure = hash('sha256', $request->get('pin'));
        $oldPinSecure = hash('sha256', $request->get('oldPin'));

        /** @var Employee $user */
        $user = Auth::guard('api-employee')->user();

        if ($user->pin_code !== $oldPinSecure) return $this->unauthorizedMessage();

        DB::beginTransaction();

        try {
            $user->pin_code = $pinSecure;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalError($e);
        }

        return $this->processSucceed();
    }
}
