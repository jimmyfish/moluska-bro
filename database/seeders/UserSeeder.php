<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::where('slug', 'admin')->get();
        $identifier = 1;
        $faker = Factory::create();

        DB::beginTransaction();

        // SUPER FVCKING ADMIN - DON'T DARE TO CHANGE IT!
        $superAdminRoles = Role::where('slug', 'super-admin')->get();
        $superId = 1;
        $superAdminRoles->each(function ($role) use ($faker, &$superId) {
            try {
                DB::table('user')->insert([
                    [
                        'name' => $faker->name(),
                        'email' => "sysadmin.$superId@bro.app",
                        'password' => Hash::make("TehPUCUK250ml"),
                        'company_id' => $role->company->id,
                        'role_id' => $role->id,
                    ]
                ]);

                DB::commit();
                $superId++;
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::warning("Adding super admin Failing at " . $exception->getMessage());
            }
        });

        $roles->each(function ($role) use(&$identifier, $faker) {
            try {
                DB::table('user')->insert([
                    'name' => $faker->name(),
                    'email' => "admin.$identifier@bro.app",
                    'password' => Hash::make('password'),
                    'company_id' => $role->company->id,
                    'role_id' => $role->id,
                ]);

                DB::commit();
                $identifier++;
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::warning("Failing : " . $exception->getMessage());
            }
        });
    }
}
