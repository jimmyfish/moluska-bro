<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginAction extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|string|email|max:100',
            'password' => 'required|string|min:1|max:255'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'meta' => [
                    'code'    => 401,
                    "message" => "The email and password did not match."
                ]
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    private function respondWithToken($token): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'meta' => [
                'code'   => 200,
                'status' => 'success'
            ],
            'data' => [
                'token'      => $token,
                'tokenType'  => 'bearer',
                'expires_in' => config('jwt.ttl'),
            ]
        ]);
    }
}
