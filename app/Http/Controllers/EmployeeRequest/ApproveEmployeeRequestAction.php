<?php

namespace App\Http\Controllers\EmployeeRequest;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeRequest;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveEmployeeRequestAction extends Controller
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

                $employeeRequest->status = 1;
                $employeeRequest->approved_at = Carbon::now();
                $employeeRequest->save();

                $notification = new Notification([
                    'title' => "Your Adv. Payment Has Been Approved",
                    'content' => "You should get the money within an hour"
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
