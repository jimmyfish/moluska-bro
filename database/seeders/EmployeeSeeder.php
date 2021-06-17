<?php

namespace Database\Seeders;

use App\Http\Services\Employee\GenerateEmployeeId;
use App\Http\Services\Faker\PhoneNumber;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\Company;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class EmployeeSeeder extends Seeder
{
    private $transformator;
    private $phoneNumberService;

    private $phoneNumbers = [];

    public function __construct(
        LocalizePhoneNumberTransformator $transformator,
        PhoneNumber $phoneNumberService
    ) {
        $this->transformator = $transformator;
        $this->phoneNumberService = $phoneNumberService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::enableQueryLog();
        $companies = Company::all();
        $employees = [];

        $faker = Factory::create();

        $companies->each(function ($company) use ($faker, &$employees) {
            $positions = $company->positions;
            $user = $company->users->first();

            if (!$positions) return true;

            $positions->each(function ($position) use ($company, $user, $faker, &$employees) {
                for ($i = 0; $i < 5; $i++) {
                    $uuid = Uuid::uuid4();
                    $phoneNumber = $this->getPhoneNumber("ID");

                    array_push($employees, [
                        'uuid'                => $uuid->toString(),
                        'name'                => ($named = $faker->name()),
                        'employee_identifier' => substr(
                            strtoupper(
                                preg_replace('/[^\da-z]/i', '', $company->name)
                            ),
                            0,
                            5
                        ) . rand(999, 999999999),
                        'email'               => Str::slug($named, '.') . "@" . explode("@", $faker->companyEmail())[1],
                        'password'            => hash('sha256', config('app.bypassOtp')),
                        'phone_number'        => $phoneNumber,
                        'position_id'         => $position->id,
                        'birthdate'           => Carbon::parse('1990-01-01'),
                        'department_id'       => $position->department->id,
                        'salary'              => (int)rand(5, 20) . "000000",
                        'company_id'          => $company->id,
                        'user_id'             => $user->id,
                        'beneficiary_name' => strtoupper($named),
                        'account_number' => (int)rand(1000000, 9999999),
                        'bank_data_id' => 10
                    ]);
                }
            });
        });

        DB::beginTransaction();
        try {
            DB::table('employee')->insert($employees);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::warning("EMPLOYEE Failing : " . $exception->getMessage());
        }
    }

    public function getPhoneNumber($localization): string
    {
        $phone = $this->transformator->transform($this->phoneNumberService->generate(), $localization);

        if (in_array($phone, $this->phoneNumbers)) return $this->getPhoneNumber($localization);

        return $phone;
    }
}
