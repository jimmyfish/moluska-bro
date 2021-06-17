<?php

namespace App\Http\Controllers\Dashboard\EmployeeRequest;

use App\Http\Controllers\Controller;
use App\Http\Services\Date\ManipulateWorkDaysService;
use App\Http\Services\EmployeeRequest\GetTransactionFeeService;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\EmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Configuration;
use App\Models\Employee;
use App\Models\Notification;

class RequestWithdrawalAction extends Controller
{
    private $transactionFeeService;
    private $workDayService;
    private $localizationTransformatorService;

    public function __construct(
        GetTransactionFeeService $transactionFeeService,
        ManipulateWorkDaysService $workDayService,
        LocalizePhoneNumberTransformator $localizationTransformatorService
        )
    {
        $this->transactionFeeService = $transactionFeeService;
        $this->workDayService = $workDayService;
        $this->localizationTransformatorService = $localizationTransformatorService;
    }

    /**
     * Withdraw type : 0 - Transfer, 1 - Withdraw
     * 
     * @param Request $request
     */
    public function do(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|int|min:10000',
            'fee' => 'required|int|min:0',
            'type' => [
                'required',
                Rule::in([0, 1])
            ],
            'pin' => 'required|numeric',
            'options' => 'required_if:type,0|array'
        ]);

        /** @var Employee $user */
        $user = Auth::guard('api-employee')->user();

        if (count($user->employeeRequests->where('status', 0)) > 0) {
            return response()->json([
                'meta' => [
                    'code' => 400,
                    'status' => 'Bad request',
                    'message' => 'User already has an active request'
                ],
            ]);
        }

        if ($user->pin_code !== hash('sha256', $request->get('pin'))) return $this->unauthorizedMessage();

        $options = $request->get('options');

        $salary = $this->workDayService->getWorkDay($user);

        $data = $request->only([
            'amount',
            'fee',
            'type'
        ]);

        if ($data['amount'] > $salary['advancedSalary']) {
            return response()->json([
                'meta' => [
                    'status' => 'Bad request',
                    'code' => 400,
                    'message' => 'Your request is exceed from earned salary'
                ],
            ]);
        }

        $fee = $this->transactionFeeService->getFee($data['amount']);

        $transactions = $user->employeeRequests->where('status', 1)->count();
        $transactionFreeLimit = Configuration::select('data->value as value')->where('slug', 'free_transaction')->first();
        # $transactionCount = ($transactions % $transactionFreeLimit->value);

        if ((int)$transactionFreeLimit->value !== 0) {
            if ((float)$fee !== (float) $data['fee']) {
                return response()->json([
                    'meta' => [
                        'status' => 'Bad request',
                        'code' => 400,
                        'message' => 'Request has been manipulated'
                    ],
                ]);
            }
        } else if ((int)$transactionFreeLimit->value === 0) {
            $data['fee'] = 0;
        }

        if (isset($options['phoneNumber'])) {
            $options['phoneNumber'] = $this->localizationTransformatorService->transform($options['phoneNumber']);
        }

        if ((!$options) && $data['type'] === 1) {
            $options = [
                'beneficiaryName' => $user->beneficiary_name ?? strtoupper($user->name),
                'accountNumber' => $user->account_number,
                'bankId' => $user->bank_data_id,
                'phoneNumber' => $user->phone_number
            ];
        }

        $employeeRequestData = [
            'employee_uuid' => $user->uuid,
            'self_withdraw' => $data['type'],
            'company_id' => $user->company_id,
            'department_id' => $user->department_id,
            'amount' => $data['amount'],
            'fee' => $data['fee'],
            'total' => $data['amount'] - $data['fee'],
            'bank_data_id' => isset($options['bankId']) ? $options['bankId'] : null,
            'account_number' => $options['accountNumber'],
            'beneficiary_name' => $options['beneficiaryName'],
            'phone_number' => $options['phoneNumber']
        ];

        $employeeRequest = new EmployeeRequest($employeeRequestData);

        DB::beginTransaction();

        try {
            $employeeRequest->save();
            
            $notification = new Notification([
                'title' => "Your Request Has Been Processed",
                'content' => "We will update you as soon as possible"
            ]);

            $user->notifications()->save($notification);

            DB::commit();

            return $this->processSucceed();
        } catch(\Exception $e) {
            Log::info('SAVING EMPLOYEE REQUEST FAILING : ' . $e->getMessage());
            return $this->internalError($e);
        }
    }
}
