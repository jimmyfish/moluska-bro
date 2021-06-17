<?php

namespace App\Http\Controllers\EmployeeRequest;

use App\Http\Controllers\Controller;
use App\Http\Enum\EmployeeRequestEnum;
use App\Models\EmployeeRequest;
use Illuminate\Http\Request;

class ShowCardStatusAction extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employeeRequests = $user->company->employeeRequests;
        $data = [];
        $statusString = EmployeeRequestEnum::STATUS_STRING;

        collect(EmployeeRequestEnum::STATUS)
            ->each(function ($status, $key) use ($statusString, $employeeRequests, &$data) {
                array_push($data, [
                    'status'       => $key,
                    'statusString' => $statusString[$key],
                    'cardValue'    => $employeeRequests->where('status', $key)->count()
                ]);
            });

        array_push($data, [
            'status'       => null,
            'statusString' => end($statusString),
            'cardValue'    => $employeeRequests->count()
        ]);

        return response()->json([
            'meta' => [
                'code'   => 200,
                'status' => 'success'
            ],
            'data' => $data
        ]);
    }
}
