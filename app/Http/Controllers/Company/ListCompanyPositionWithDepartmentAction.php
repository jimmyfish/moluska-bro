<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListCompanyPositionWithDepartmentAction extends Controller
{
    public function __invoke(Request $request)
    {
        $department = Department::find($request->id);

        if (!$department) return $this->resourceNotFound();

        if (Auth::user()->company->id !== $department->company->id) return $this->unauthorizedMessage();

        $positions = $department->positions;

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success'
            ],
            'data' => $positions,
        ]);
    }
}
