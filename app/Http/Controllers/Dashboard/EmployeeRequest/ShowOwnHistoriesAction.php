<?php

namespace App\Http\Controllers\Dashboard\EmployeeRequest;

use App\Http\Controllers\Controller;
use App\Http\Enum\EmployeeRequestEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Repositories\EmployeeRequest\EmployeeRequestRepositoryInterface;

class ShowOwnHistoriesAction extends Controller
{
    private $employeeRequestRepositories;

    public function __construct(
        EmployeeRequestRepositoryInterface $employeeRequestRepositories
    )
    {
        $this->employeeRequestRepositories = $employeeRequestRepositories;
    }

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'page' => 'nullable|numeric',
            'perPage' => 'nullable|numeric',
            'status' => 'nullable|numeric'
        ]);

        /** @var Employee $user */
        $user = Auth::guard('api-employee')->user();

        $requestHistories = $this->employeeRequestRepositories
            ->getEmployeeOwnHistories(
                $user,
                $request->has('status') && $request->get('status') !== "" ?
                    (int) $request->get('status') :
                    null
            )
            ->orderBy('created_at', "DESC")
            ->paginate($request->get('perPage') ?? 20);

        collect($requestHistories->items())->each(function ($history) {
            $history->statusString = EmployeeRequestEnum::STATUS[$history->status];
        });

        return response()->json([
            'meta' => [
                'code'     => 200,
                'status'   => 'success',
                'paginate' => [
                    'totalData' => $requestHistories->total(),
                    'perPage'   => $requestHistories->perPage(),
                    'page'      => $requestHistories->currentPage(),
                    'lastPage'  => $requestHistories->lastPage()
                ],
            ],
            'data' => $requestHistories->items()
        ]);
    }
}
