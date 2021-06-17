<?php


namespace App\Http\Services\Employee;


class GenerateEmployeeId
{
    public function generate(int $length = 20): string
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charLen = strlen($characters);
        $randomString = "";

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLen - 1)];
        }

        return $randomString;
    }
}
