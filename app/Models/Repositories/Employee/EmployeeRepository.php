<?php

namespace App\Models\Repositories\Employee;

use App\Models\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    private $model;

    public function __construct(Employee $model)
    {
        $this->model = $model;
    }

    public function getTotalEmployee()
    {
        return $this->model->where([

        ]);
    }

    public function getEmployee()
    {
        return $this->model->where([

        ]);
    }

    public function getAllEmployee(
        $companyId,
        ?string $searchParam = null,
        $departments = [],
        $positions = []
    )
    {
        $result = $this->model->where('company_id', $companyId);

        if ($searchParam) {
            $result = $result->where('name', 'like', "%$searchParam%")
                ->orWhere('employee_identifier', 'like', "%$searchParam%");
        }

        if ($departments) {
            $deptIds = is_string($departments) ? explode(",", $departments) : $departments;

            if (is_array($deptIds)) {
                # Removing non numeric ids
                foreach ($deptIds as $key => $id) {
                    if (!is_numeric($id)) unset($deptIds[$key]);
                }

                $result = $result->whereIn('department_id', $deptIds);
            } else if (is_int($deptIds)) {
                $result = $result->where('department_id', $deptIds);
            }
        }

        if ($positions) {
            $positionIds = is_string($positions) ? explode(",", $positions) : $positions;

            if (is_array($positionIds)) {
                # Removing non numeric ids
                foreach ($positionIds as $key => $id) {
                    if (!is_numeric($id)) unset($positionIds[$key]);
                }

                $result = $result->whereIn('position_id', $positionIds);
            } else if (is_int($positionIds)) {
                $result = $result->where('position_id', $positionIds);
            }
        }

        return $result;
    }
}
