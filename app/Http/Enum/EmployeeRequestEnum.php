<?php


namespace App\Http\Enum;


class EmployeeRequestEnum
{
    public const STATUS = [
        0 => 'Waiting Approval',
        1 => 'Approved',
        2 => 'Rejected'
    ];

    /**
     * @var array STATUS_STRING
     */
    public const STATUS_STRING = [
        0 => 'Waiting Approval',
        1 => 'Request Approved',
        2 => 'Request Rejected',
        3 => 'Total Request'
    ];
}
