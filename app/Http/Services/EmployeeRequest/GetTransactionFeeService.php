<?php

namespace App\Http\Services\EmployeeRequest;

class GetTransactionFeeService
{
    public function getFee($amount)
    {
        $feePercentage = 0;

        if (50000 <= $amount && $amount <= 500000) {
            $feePercentage = 1.2;
        } else if (500000 < $amount && $amount <= 1000000) {
            $feePercentage = 2;
        } else if (1000000 < $amount && $amount <= 2000000) {
            $feePercentage = 2.5;
        } else if (2000000 < $amount && $amount <= 5000000) {
            $feePercentage = 3;
        } else if (5000000 < $amount && $amount <= 10000000) {
            $feePercentage = 4;
        }

        return round(($feePercentage / 100) * $amount);
    }
}