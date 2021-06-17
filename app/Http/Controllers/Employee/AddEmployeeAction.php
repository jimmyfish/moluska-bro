<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Http\Services\Photo\ImageProcessingService;
use App\Http\Services\Typography\CreateRandomCharWithCustomLengthService;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class AddEmployeeAction extends Controller
{
    private $transformator;
    private $imageProcessingService;
    private $typographyService;

    public function __construct(
        LocalizePhoneNumberTransformator $transformator,
        ImageProcessingService $imageProcessingService,
        CreateRandomCharWithCustomLengthService $typographyService
    )
    {
        $this->transformator = $transformator;
        $this->imageProcessingService = $imageProcessingService;
        $this->typographyService = $typographyService;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'           => 'required|string|min:2|max:50',
            'phoneNumber'    => 'required|string|max:20',
            'email'          => 'required|email|unique:employee',
            'salary'         => 'required|integer|min:1',
            'birthdate'      => 'required|date_format:d/m/Y',
            'departmentId'   => 'required|integer|min:1',
            'positionId'     => 'required|integer|min:1',
            'profilePicture' => 'nullable|max:2048',
            'beneficiaryName' => 'nullable|string|min:1',
            'accountNumber' => 'required|string|min:1',
            'bankId' => 'required|int'
        ]);

        $user = auth()->user();
        $userCompany = $user->company;

        $department = $userCompany->departments
            ->find($request->get('departmentId'));

        if (!$department) return $this->resourceNotFound();

        $position = $userCompany->positions
            ->where('department_id', $department->id)
            ->find($request->get('positionId'));

        if (!$position) return $this->resourceNotFound();

        DB::beginTransaction();
        try {
            $imageName = null;
            if ($request->has('profilePicture')) {
                $file = $request->file('profilePicture');
                $imageName = md5(uniqid()) . "." . $file->extension();

                $file->move(base_path('public/images'), $imageName);
            }

            $employee = new Employee([
                'uuid'          => Uuid::uuid4(),
                'name'          => $request->get('name'),
                'phone_number'  => $this->transformator->transform($request->get('phoneNumber')),
                'email'         => $request->get('email'),
                'salary'        => (int)$request->get('salary'),
                'birthdate'     => Carbon::createFromFormat("d/m/Y", $request->get('birthdate'))
                    ->format("Y-m-d"),
                'password'      => hash('sha256', config('app.bypassOtp')),
                'department_id' => $department->id,
                'position_id'   => $position->id,
                'company_id'    => $userCompany->id,
                'user_id'       => $user->id,
                'beneficiary_name' => $request->get('beneficiaryName'),
                'account_number' => $request->get('accountNumber'),
                'bank_data_id' => $request->get('bankId'),
                'profile_picture' => $imageName,
            ]);

           $employee->save();
           DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();

            if ($e->errorInfo[1] === 1062) return $this->phoneDuplicate();
        }

        return $this->processSucceed();
    }
}
