<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function processSucceed()
    {
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => "Success",
                'message' => "Request completed successfully"
            ],
            'data' => [],
        ], 200);
    }

    public function doesntBelongTo()
    {
        return response()->json([
            'meta' => [
                'code' => 401,
                'status' => "Bad Request",
                'message' => "You don't have enough privilege to do this action"
            ],
            'data' => [],
        ], 401);
    }

    public function resourceNotFound()
    {
        return response()->json([
            'meta' => [
                'code' => 400,
                'status' => "Bad Request",
                'message' => "Resource not found"
            ],
            'data' => [],
        ], 400);
    }

    public function resourceEqual()
    {
        return response()->json([
            'meta' => [
                'code' => 400,
                'status' => "Bad Request",
                'message' => "Resource not in pending state, exiting."
            ],
            'data' => [],
        ], 400);
    }

    public function phoneDuplicate()
    {
        return response()->json([
            "phone_number" => [
                "The phone number has already been taken."
            ],
        ], 500);
    }

    public function unauthorizedMessage()
    {
        return response()->json([
            'meta' => [
                'code' => 401,
                'status' => "Unauthorized"
            ],
            'data' => [],
        ], 401);
    }

    public function illegalBody()
    {
        return response()->json([
            'meta' => [
                'code' => 400,
                'status' => "Bad Request",
                'message' => "Request isn't understand by server or it contains illegal character."
            ],
            'data' => [],
        ], 400);
    }

    public function illegalMimeType()
    {
        return response()->json([
            'meta' => [
                'code' => 400,
                'status' => "image_mime_type_violation",
                'message' => "Your file format is not supported"
            ],
            'data' => [],
        ], 400);
    }

    public function exceedSizeLimit()
    {
        return response()->json([
            'meta' => [
                'code' => 400,
                'status' => "image_exceed_size_limit",
                'message' => "Your file is exceed maximum limit"
            ],
            'data' => [],
        ], 400);
    }

    public function internalError(\Exception $e)
    {
        Log::info($e->getMessage());
        
        return response()->json([
            'meta' => [
                'code' => 500,
                'status' => 'Internal server error',
                'message' => $e->getMessage()
            ]
        ]);
    }
}
