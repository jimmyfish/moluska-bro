<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = Employee::select([
            "uuid", "name", "company_id", "department_id"
        ])->get();

        $dataToBeRequested = [];

        for ($i = 0; $i < 750; $i++) {
            $arrEmployees = $employees->toArray();
            $randData = $employees[array_rand($arrEmployees)];
            array_push($dataToBeRequested, [
                'employee_uuid' => $randData->uuid,
                'company_id'    => $randData->company_id,
                'department_id' => $randData->department_id,
                'amount'        => (int)rand(2, 10) . "00000",
                'beneficiary_name' => "HOMO SAPIENS",
                'account_number' => "0111311985"
            ]);

            // Popping duplicate
            $find = array_search($randData->uuid, array_column($arrEmployees, 'uuid'));
            unset($employees[$find]);
        }

        DB::beginTransaction();
        try {
            DB::table('employee_request')->insert($dataToBeRequested);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::warning("EMPLOYEE REQUEST Failing : " . $exception->getMessage());
        }
    }
}
