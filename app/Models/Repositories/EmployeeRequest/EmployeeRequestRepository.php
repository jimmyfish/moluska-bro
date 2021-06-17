<?php

namespace App\Models\Repositories\EmployeeRequest;

use App\Models\EmployeeRequest;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeRequestRepository implements EmployeeRequestRepositoryInterface
{
    private $model;

    public function __construct(EmployeeRequest $model)
    {
        $this->model = $model;
    }

    public function getPendingRequest()
    {
        return $this->model->where([
            'status' => 0
        ]);
    }

    public function getCompanyPendingRequest()
    {
        return $this->model->where([
            'company_id' => auth()->user()->company_id,
            'status'     => 0
        ]);
    }

    public function getCompanyAllData(
        $companyId,
        ?string $searchParam = null,
        $department = null,
        ?array $requestDate = [],
        ?array $amount = [],
        ?int $status = null
    )
    {
        $result = $this->model->where('employee_request.company_id', $companyId);

        if ($status !== null && $status !== "") $result = $result->where('status', $status);

        if ($searchParam) {
            $result = $result->join('employee as E', 'employee_request.employee_uuid', '=', 'E.uuid')
                ->where('E.name', 'like', "%$searchParam%")
                ->orWhere('E.employee_identifier', 'like', "%$searchParam%");
        }

        if ($department) {
            $deptIds = is_string($department) ? explode(",", $department) : $department;

            if (is_array($deptIds)) {
                foreach ($deptIds as $key => $id) {
                    if (!is_numeric($id)) unset($deptIds[$key]);
                }

                $result = $result->whereIn('employee_request.department_id', $deptIds);
            } elseif (is_int($deptIds)) {
                $result = $result->where('employee_request.department_id', $deptIds);
            }
        }

        if ($requestDate) {
            if (isset($requestDate['start']) && isset($requestDate['end'])) {
                if (Carbon::parse($requestDate['start'])->greaterThan(Carbon::parse($requestDate['end']))) {
                    $requestDate['start'] = $requestDate['end'];
                }
            }

            if (isset($requestDate['start'])) {
                $result->where('employee_request.created_at', '>', $requestDate['start']);
            }

            if (isset($requestDate['end'])) {
                $result->where('employee_request.created_at', '<', $requestDate['end']);
            }
        }

        if ($amount) {
            if (!isset($amount['min'])) $amount['min'] = 0;
            if (isset($amount['min']) && isset($amount['max'])) if ($amount['min'] > $amount['max']) unset($amount['max']);

            if (isset($amount['min'])) $result = $result->where('employee_request.amount', '>=', $amount['min']);
            if (isset($amount['max'])) $result = $result->where('employee_request.amount', '<=', $amount['max']);
        }

        return $result->select('employee_request.*');
    }

    public function getEmployeeOwnHistories(Employee $employee, ?int $status = null)
    {
        $return = $this->model->where('employee_uuid', $employee->uuid);

        if ($status !== null && $status !== "") $return->where('status', $status);

        return $return;
    }
}
