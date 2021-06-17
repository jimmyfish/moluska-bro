<?php

namespace App\Http\Controllers\EmployeeRequest;

use App\Http\Controllers\Controller;
use App\Http\Enum\EmployeeRequestEnum;
use App\Models\Repositories\EmployeeRequest\EmployeeRequestRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ListAllEmployeeRequestByCompanyAction extends Controller
{
    private $employeeRequestRepository;

    public function __construct(EmployeeRequestRepositoryInterface $employeeRequestRepository)
    {
        $this->employeeRequestRepository = $employeeRequestRepository;
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'departments'       => 'string|nullable',
            'perPage'          => 'numeric|min:5|nullable',
            'page'             => 'numeric|min:1|nullable',
            'searchParam'      => 'string|nullable|max:50',
            'amountMin'        => 'numeric|nullable|min:0',
            'amountMax'        => 'numeric|nullable|gte:' . ($request->get('amountMin') ?? 0),
            'requestDateStart' => 'date|nullable',
            'requestDateEnd'   => 'date|nullable|after_or_equal:requestDateStart',
            'status'           => [
                'int',
                Rule::in(
                    array_keys(EmployeeRequestEnum::STATUS)
                ),
                'nullable'
            ],
        ]);

        $transactions = $this->employeeRequestRepository
            ->getCompanyAllData(
                auth()->user()->company->id,
                $request->get('searchParam'),
                $request->get('departments'),
                [
                    'start' => $request->get('requestDateStart'),
                    'end'   => $request->get('requestDateEnd'),
                ],
                [
                    'min' => $request->get('amountMin'),
                    'max' => $request->get('amountMax'),
                ],
                $request->has('status') && $request->get('status') !== "" ?
                    (int) $request->get('status') :
                    null
            )
            ->orderBy('id', 'DESC')
            ->paginate((int)$request->get('perPage') ?? 20);

        collect($transactions->items())->each(function ($item) {
            $employee = $item->employee;
            $item->employeeIdentifier = $employee->employee_identifier;
            $item->statusString = EmployeeRequestEnum::STATUS[$item->status];
            $item->createdAt = Carbon::parse($item->created_at);
            $item->employeeId = $employee->id;
            $item->employeeName = $employee->name;
            $item->department = $employee->department->name;

            unset($item->employee);
        });

        return response()->json([
            'meta' => [
                'code'     => 200,
                'status'   => 'success',
                'paginate' => [
                    'totalData' => $transactions->total(),
                    'perPage'   => $transactions->perPage(),
                    'page'      => $transactions->currentPage(),
                    'lastPage'  => $transactions->lastPage()
                ],
            ],
            'data' => $transactions->items()
        ]);
    }
}
