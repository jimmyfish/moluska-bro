<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Enum\DepartmentAndPositionEnum;
use App\Http\Enum\RoleEnum;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\Company;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    private $transformator;

    public function __construct(LocalizePhoneNumberTransformator $transformator)
    {
        $this->transformator = $transformator;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8
        DB::beginTransaction();

        $faker = Factory::create();

        /*
        try {

        }catch (\Exception $exception) {
            DB::rollBack();
            Log::warning("Failing importing reason : " . $exception->getMessage());
        }
        */
        $companies = [];
        // Seeding 10 companies
        try {
            for ($i = 0; $i < 10; $i++) {
                array_push($companies, [
                    'id' => $i + 1,
                    'name' => ($named = $faker->company()),
                    'slug' => Str::slug($named),
                    'phone_number' => $this->transformator->transform($faker->phoneNumber(), "US"),
                    'email' => Str::slug($named, '.') . "@" . $faker->freeEmailDomain()
                ]);
            }

            DB::table('company')->insert($companies);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::warning("Failing importing reason : " . $exception->getMessage());
        }

        // Seeding Department and it's position(s)
        try {
            $key = 1;
            foreach (DepartmentAndPositionEnum::DEPT_HIERARCHY as $department => $positions) {
                foreach ($companies as $company) {
                    $dept = [
                        'id'         => $key,
                        'name'       => $department,
                        'slug' => Str::slug($department),
                        'company_id' => $company['id']
                    ];
    
                    DB::table('department')->insert($dept);
    
                    $key++;
    
                    foreach ($positions as $k => $position) {
                        DB::table('position')->insert([
                            'name'          => $position,
                            'description'   => "Lorem ipsum",
                            'department_id' => $dept['id'],
                            'company_id'    => $company['id']
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::warning("Failing importing reason : " . $exception->getMessage());
        }

        $companies = Company::all();

        // Seeding Role
        try {
            $companies->each(function ($company) {
                foreach (RoleEnum::ROLES as $role) {
                    DB::table('role')->insert([
                        'title' => $role,
                        'description' => 'Some decription',
                        'slug' => Str::slug($role),
                        'company_id' => $company->id,
                    ]);
                }
            });
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::warning("Failing importing reason : " . $exception->getMessage());
        }
    }
}
