<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Services\EmployeeRequest\GetCompanyPendingRequest;
use App\Models\Repositories\EmployeeRequest\EmployeeRequestRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetDashboardDataAction extends Controller
{
    private $companyPendingRequest;

    public function __construct(
        GetCompanyPendingRequest $companyPendingRequest
    )
    {
        $this->companyPendingRequest = $companyPendingRequest;
    }

    public function __invoke(): JsonResponse
    {
        $transactions = $this->companyPendingRequest->getCompanyPendingRequest();

        $user = auth()->user();
        $company = $user->company;
        $employeeRequests = $company->employeeRequests;

        $transactions->each(function ($transaction) {
            $transaction->employeeIdentifier = $transaction->employee->employee_identifier;
            unset($transaction->employee);
        });
        
        return response()->json([
            'meta' => [
                'code'   => 200,
                'status' => 'success',
                'profile' => [
                    'name' => $user->name,
                    'companyName' => $company->name,
                    'companyEmail' => $company->email,
                    'companyPhoneNumber' => $company->phone_number,
                    'position' => $user->role->title,
                ],
                'statistics' => [
                    'totalEmployee' => $company->employees->count(),
                    'totalAdvanceSalaryTaken' => $employeeRequests->where('status', 1)->sum('amount'),
                    'numberOfRequest' => $employeeRequests->count()
                ]
            ],
            'data' => [
                'transactionCount' => count($transactions),
                'transactions' => $transactions,
            ]
        ]);
    }
}
