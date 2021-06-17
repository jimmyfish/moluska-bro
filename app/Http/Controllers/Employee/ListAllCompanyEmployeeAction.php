<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Http\Request;

class ListAllCompanyEmployeeAction extends Controller
{
    private $employeeRepository;
    private $localizePhoneNumberTransformator;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepository,
        LocalizePhoneNumberTransformator $localizePhoneNumberTransformator
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->localizePhoneNumberTransformator = $localizePhoneNumberTransformator;
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'departments' => 'string|nullable',
            'perPage'     => 'numeric|min:5|nullable',
            'page'        => 'numeric|min:1|nullable',
            'searchParam' => 'string|nullable|max:50',
            'positions'   => 'string|nullable'
        ]);

        $employees = $this->employeeRepository->getAllEmployee(
            auth()->user()->company->id,
            $request->get('searchParam'),
            $request->get('departments'),
            $request->get('positions')
        )
            ->orderBy('id', 'ASC')
            ->paginate((int) $request->get('perPage') ?? 20);

        $employees->each(function ($employee) {
            $employee->employeeIdentifier = $employee->employee_identifier;
            $employee->departmentName = $employee->department->name;
            $employee->positionName = $employee->position->name;
            $employee->createdAt = $employee->created_at;
            $employee->phoneNumber = $this->localizePhoneNumberTransformator->transform($employee->phone_number, $employee->country_iso_code);
            $employee->isResigned = $employee->is_resigned;
            $employee->resignedAt = $employee->resigned_at;

            unset($employee->department);
            unset($employee->position);
        });

        return response()->json([
            'meta' => [
                'code'     => 200,
                'status'   => 'success',
                'paginate' => [
                    'totalData' => $employees->total(),
                    'perPage'   => $employees->perPage(),
                    'page'      => $employees->currentPage(),
                    'lastPage'  => $employees->lastPage()
                ],
            ],
            'data' => $employees->items()
        ]);
    }
}
