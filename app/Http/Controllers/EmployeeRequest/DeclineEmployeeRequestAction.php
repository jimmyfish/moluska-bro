<?php

namespace App\Http\Controllers\EmployeeRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeeRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\Notification;
use App\Models\Employee;

class DeclineEmployeeRequestAction extends Controller
{
    public function __invoke(Request $request)
    {
        $ids = is_string($request->get('id')) ?
            explode(',', $request->get('id')) :
            $request->get('id');

        $employeeRequests = auth()->user()->company->employeeRequests
            ->where('status', 0)
            ->whereIn('id', $ids);

        DB::beginTransaction();
        try {
            $employeeRequests->each(function ($employeeRequest) {
                if (!$employeeRequest) return $this->resourceNotFound();

                if (!$employeeRequest->hasAbilityToApprove()) return $this->doesntBelongTo();

                $employeeRequest->status = 2;
                $employeeRequest->approved_at = Carbon::now();
                $employeeRequest->save();

                $notification = new Notification([
                    'title' => "Your Adv. Payment Has Been Rejected",
                    'content' => "Please contact your admin for more info"
                ]);

                $employee = Employee::find($employeeRequest->employee->id);

                $employee->notifications()->save($notification);
            });

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
        return $this->processSucceed();
    }
}
