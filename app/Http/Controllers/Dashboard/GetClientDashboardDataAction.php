<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Date\ManipulateWorkDaysService;
use App\Models\Configuration;
use App\Models\EmployeeRequest;
use App\Models\Employee;

class GetClientDashboardDataAction extends Controller
{
    private $manipulateWorkDaysService;

    public function __construct(ManipulateWorkDaysService $manipulateWorkDaysService)
    {
        $this->manipulateWorkDaysService = $manipulateWorkDaysService;
    }

    public function index()
    {
        $salary = $this->manipulateWorkDaysService->getWorkDay(Auth::guard('api-employee')->user());

        /** @var Employee $user */
        $user = auth()->guard('api-employee')->user();

        $user->hasPin = $user->pin_code ? true : false;
        $user->hasSavedContact = $user->lastContact ? true : false;

        $historiesApproved = EmployeeRequest::where('employee_uuid', $user->uuid)->where('status', 1)->get();
        $histories = EmployeeRequest::where('employee_uuid', $user->uuid)->orderBy('created_at', 'desc')->get();

        $user->salaryLeft = $user->salary - $historiesApproved->sum('amount');
        $user->profile_picture = $user->profile_picture ? 
            config('app.url') . config('app.imageURI') . $user->profile_picture :
            null;

        unset($user->lastContact);

        $transactions = $histories->where('status', 1)->count();
        $transactionFreeLimit = Configuration::select('data->value as value')->where('slug', 'free_transaction')->first();
        $transactionCount = ($transactions % $transactionFreeLimit->value);

        return response()->json([
            'meta' => [
                'profile' => $user,
                'availableToBeWithdrawn' => $salary['advancedSalary'],
                'validWorkdays' => $salary['workDays'],
                'nextPayday' => Carbon::parse($salary['payday'])->format('Y-m-d'),
                'transactionLeft' => $transactionCount,
                'transactionFreeLimit' => (int) $transactionFreeLimit->value - 1,
                'unreadNotificationsCount' => $user->notifications->where('state', 0)->count(),
            ],
            'data' => [
                'histories' => $histories->take(3),
            ]
        ]);
    }
}
