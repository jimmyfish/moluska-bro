<?php

namespace App\Http\Services\EmployeeRequest;

use App\Http\Enum\EmployeeRequestEnum;
use App\Models\Repositories\EmployeeRequest\EmployeeRequestRepositoryInterface;

class GetCompanyPendingRequest
{
    private $employeeRequestRepository;

    public function __construct(
        EmployeeRequestRepositoryInterface $employeeRequestRepository
    ) {
        $this->employeeRequestRepository = $employeeRequestRepository;
    }

    public function getCompanyPendingRequest($newer = true, $limit = 10)
    {
        $transactions = $this->employeeRequestRepository->getCompanyPendingRequest();

        if ($newer) {
            $transactions = $transactions->orderBy("id", "DESC");
        }
        $transactions = $transactions
            ->limit($limit)
            ->get();

        $transactions->each(function ($transaction) {
            $transaction->employeeId = $transaction->employee->id;
            $transaction->employeeName = $transaction->employee->name;
            $transaction->statusString = EmployeeRequestEnum::STATUS[$transaction->status];
            unset($transaction->employee);
        });

        return $transactions;
    }
}
