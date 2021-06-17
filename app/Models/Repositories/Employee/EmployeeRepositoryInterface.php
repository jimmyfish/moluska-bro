<?php

namespace App\Models\Repositories\Employee;

interface EmployeeRepositoryInterface
{
    public function getTotalEmployee();

    public function getAllEmployee($companyId, ?string $searchParam = null, $departments = [], $positions = []);
}
