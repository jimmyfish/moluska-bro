<?php

namespace App\Models\Repositories\EmployeeRequest;

use App\Models\Employee;

interface EmployeeRequestRepositoryInterface
{
    public function getPendingRequest();

    public function getCompanyPendingRequest();

    public function getCompanyAllData(
        $companyId,
        string $searchParam = null,
        $department = [],
        ?array $requestDate = [],
        ?array $amount = [],
        ?int $status = null
    );

    public function getEmployeeOwnHistories(Employee $employee, ?int $status = null);
}
