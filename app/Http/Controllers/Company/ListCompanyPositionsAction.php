<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListCompanyPositionsAction extends Controller
{
    public function index()
    {
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success'
            ],
            'data' => auth()->user()->company->positions
        ]);
    }
}
