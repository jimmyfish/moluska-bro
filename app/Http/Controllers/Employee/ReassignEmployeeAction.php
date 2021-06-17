<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReassignEmployeeAction extends Controller
{
    public function patch(Request $request)
    {
        $employee = auth()->user()->company->employees->find($request->id);

        if (!$employee) return $this->resourceNotFound();

        $employee->is_resigned = 0;
        $employee->resigned_at = null;
        $employee->phone_number = $employee->phone_number_last;
        $employee->phone_number_last = null;
        $employee->employee_identifier = $employee->employee_identifier_old;
        $employee->employee_identifier_old = null;

        DB::beginTransaction();

        try {
            $employee->save();

            DB::commit();
        }catch (QueryException $exception) {
            DB::rollBack();

            return response()->json([
                'meta' => [
                    'message' => $exception->getMessage()
                ]
            ], 500);
        }

        return $this->processSucceed();
    }
}
