<?php

namespace Database\Seeders;

use App\Models\EmployeeRequest;
use App\Models\LastContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SavedContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        # Get transfer-to-bro data for data faking
        $employeeRequests = EmployeeRequest::select([
            'employee_uuid',
            'beneficiary_name',
            'account_number',
            'bank_data_id'
        ])
            ->where('self_withdraw', 0)
            ->get();
        
        $lastContacts = [];
        
        $employeeRequests->each(function ($employeeRequest) use (&$lastContacts) {
            $lastContacts[] = [
                'employee_uuid' => $employeeRequest->employee_uuid,
                'beneficiary_name' => $employeeRequest->beneficiary_name,
                'account_number' => $employeeRequest->account_number,
                'bank_data_id' => $employeeRequest->bank_data_id,
            ];
        });

        DB::beginTransaction();

        try {
            DB::table('last_contact')->insert($lastContacts);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("Failing process data at : " . $e->getMessage());
        }
    }
}
