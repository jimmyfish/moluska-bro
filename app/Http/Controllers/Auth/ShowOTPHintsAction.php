<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\Configuration;
use App\Models\Employee;
use Illuminate\Http\Request;

class ShowOTPHintsAction extends Controller
{
    private $transformator;

    public function __construct(LocalizePhoneNumberTransformator $transformator)
    {
        $this->transformator = $transformator;
    }

    public function show(Request $request)
    {
        $this->validate($request, [
            'phoneNumber' => 'required|string|min:1|max:20',
            'token' => 'required|string|min:1|max:20',
            'localization' => 'string|min:1|max:3'
        ]);

        $data = $request->only([
            'phoneNumber',
            'token'
        ]);

        $data['phoneNumber'] = $this->transformator->transform($data['phoneNumber'], $request->get('localization'));
        $storedToken = Configuration::select([
            'key',
            'data->token as token',
            'slug'
        ])->where('slug', 'public_token')->first();
        
        if ($data['token'] !== $storedToken->token) return $this->doesntBelongTo();

        $employee = Employee::where('phone_number', $data['phoneNumber'])->first();

        if (!$employee) return $this->resourceNotFound();

        if(!$employee->otpCode) return $this->resourceNotFound();

        $otpCode = $employee->otpCode->code;

        return response()->json([
            'meta' => [
                'status' => 'success',
                'code' => 200
            ],
            'data' => [
                'phoneNumber' => $data['phoneNumber'],
                'otpCode' => $otpCode
            ]
        ]);
    }
}
