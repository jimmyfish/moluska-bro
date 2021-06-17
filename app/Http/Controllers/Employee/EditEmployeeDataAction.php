<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EditEmployeeDataAction extends Controller
{
    private array $mutable = [
        'name',
        'phoneNumber',
        'email',
        'salary',
        'birthdate',
        'departmentId',
        'positionId',
        'countryIsoCode',
        'beneficiaryName',
        'accountNumber',
        'bankId',
        'profilePicture'
    ];

    private $transformator;

    public function __construct(LocalizePhoneNumberTransformator $transformator)
    {
        $this->transformator = $transformator;
    }

    public function put(Request $request)
    {
        try {
            $userCompany = auth()->user()->company;
            $this->validate($request, [
                'name'         => 'nullable|string|min:2|max:50',
                'phoneNumber'  => 'nullable|string|max:20',
                'email'        => 'nullable|email', // create custom validation logic for his/her own email (CONFLICT BLOK!)
                'salary'       => 'nullable|numeric|min:1',
                'birthdate'    => 'nullable|string',
                'departmentId' => 'nullable|integer|min:1',
                'positionId'   => 'nullable|integer|min:1',
                'countryIsoCode' => 'nullable|string|min:1|max:5',
                'beneficiaryName' => 'nullable|string|min:1',
                'accountNumber' => 'nullable|string|min:1',
                'bankId' => 'nullable|int',
                'profilePicture' => 'required|image|mimes:jpg,png,jpeg|max:2048'
            ]);

            # Remove limiter if multi-national
            if ($request->has('countryIsoCode') && $request->get('countryIsoCode') !== "ID") return $this->illegalBody();

            $employee = $userCompany->employees
                ->find($request->id);

            if ($employee->hasBeenResigned()) return $this->illegalBody();

            if (!$employee) return $this->resourceNotFound();

            $putData = $request->only($this->mutable);

            foreach ($putData as $column => $value) {
                if ($column === 'birthdate') {
                    $employee->birthdate = Carbon::createFromFormat("d/m/Y", $value)->format("Y-m-d");
                    continue;
                }

                if ($column === 'departmentId') {
                    $department = $userCompany->departments
                        ->find($request->get('departmentId'));

                    if (!$department) return $this->resourceNotFound();
                }

                if ($column === 'positionId') {
                    $position = $userCompany->positions;

                    if ($request->has('departmentId')) {
                        $position = $position->where('department_id', $request->get('departmentId'));
                    } else {
                        $position = $position->where('department_id', $employee->department_id);
                    }

                    $position = $position->find($request->get('positionId'));

                    if (!$position) return $this->resourceNotFound();
                }

                if ($column === 'bankId') {
                    $employee->bank_data_id = $value;
                }

                if ($column === 'phoneNumber' && $value !== $employee->phone_number) {
                    $isoPhoneNumber = $this->transformator->transform($value);
                    if ($isoPhoneNumber !== $employee->phone_number) {
                        $employee->phone_number_last = $employee->phone_number;
                    }
                    $employee->phone_number = $isoPhoneNumber;
                    continue;
                }

                if ($column === 'countryIsoCode' && $value !== $employee->country_iso_code) {
                    $employee->country_iso_code_last = $employee->country_iso_code;
                }

                if ($column === "profilePicture") {
                    if ($employee->profile_picture) {
                        $employee->profile_picture_old = $employee->profile_picture;
                        unlink(base_path('public/images') . "/" . $employee->profile_picture_old);
                    }

                    $file = $request->file('profilePicture');
                    $imageName = md5(uniqid()) . "." . $file->extension();

                    $file->move(base_path('public/images'), $imageName);
                    $employee->profile_picture = $imageName;
                    continue;
                }

                $column = Str::snake($column);

                if (isset($employee->{$column})) {
                    if ($value && ($employee->{$column} !== $value)) {
                        $employee->{$column} = $value;
                    }
                }
            }

            DB::beginTransaction();
            try {
                $employee->save();

                DB::commit();
            } catch (QueryException $exception) {
                DB::rollBack();

                return response()->json($exception->getMessage());
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

        return $this->processSucceed();
    }
}
