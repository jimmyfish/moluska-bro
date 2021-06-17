<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResigningEmployeeAction extends Controller
{
    public function delete(Request $request)
    {
        $employee = auth()->user()->company->employees->find($request->id);

        if (!$employee) return $this->resourceNotFound();

        $employee->is_resigned = 1;
        $employee->resigned_at = Carbon::now();
        $employee->phone_number_last = $employee->phone_number;
        $employee->phone_number = null;
        $employee->employee_identifier_old = $employee->employee_identifier;
        $employee->employee_identifier = null;

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
