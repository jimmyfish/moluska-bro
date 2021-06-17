<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestWithPictureAction extends Controller
{
    public function index(Request $request)
    {
        if (!$request->hasHeader('Content-Type') === 'multipart/form-data')  return response()->json(['meta' => ['message' => "Images needed"]]);

        if (!$request->hasFile('profile_picture')) return response()->json(['meta' => ['message' => "Images needed"]]);

        $file = $request->file('profile_picture');

        if (!$file->isValid())  return response()->json(['meta' => ['message' => "Images needed"]]);

        dd($file->getClientMimeType());
    }
}
