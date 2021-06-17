<?php

namespace App\Http\Controllers;

use App\Http\Enum\DepartmentAndPositionEnum;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function faker()
    {
        foreach (DepartmentAndPositionEnum::DEPT_HIERARCHY as $department => $positions) {

            try {
                return response()->json($department);
//                    DB::table('department')->insert();
            }catch (\Exception $exception) {

            }
        }
    }
}
