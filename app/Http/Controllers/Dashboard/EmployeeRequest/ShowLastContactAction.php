<?php

namespace App\Http\Controllers\Dashboard\EmployeeRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowLastContactAction extends Controller
{
    public function __invoke()
    {
        $user = Auth::guard('api-employee')->user();

        $lastContact = $user->lastContact;

        if (!$lastContact) return $this->resourceNotFound();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
            ],
            'data' => $lastContact,
        ]);
    }
}
