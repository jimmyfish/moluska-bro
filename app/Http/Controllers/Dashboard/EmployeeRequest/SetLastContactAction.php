<?php

namespace App\Http\Controllers\Dashboard\EmployeeRequest;

use App\Http\Controllers\Controller;
use App\Models\BankList;
use App\Models\LastContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetLastContactAction extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'beneficiaryName' => 'required|string',
            'accountNumber' => 'required|string',
            'bankId' => 'required|int',
            'phoneNumber' => 'required|string|min:1'
        ]);

        $user = Auth::guard('api-employee')->user();

        $lastContact = $user->lastContact;

        DB::beginTransaction();

        if ($lastContact) {
            $user->lastContact->delete();
        }

        $data = $request->only([
            'beneficiaryName',
            'accountNumber',
            'bankId',
            'phoneNumber'
        ]);

        $bankData = BankList::find($data['bankId']);

        if (!$bankData) return $this->resourceNotFound();

        try {
            $lastContact = new LastContact([
                'employee_uuid' => $user->uuid,
                'beneficiary_name' => strtoupper($data['beneficiaryName']),
                'account_number' => $data['accountNumber'],
                'bank_data_id' => $data['bankId'],
                'phone_number' => $data['phoneNumber']
            ]);
            $lastContact->save();

            DB::commit();
            return $this->processSucceed();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalError($e);
        }
    }
}
