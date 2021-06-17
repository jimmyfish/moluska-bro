<?php


namespace App\Http\Enum;


use MyCLabs\Enum\Enum;

class DepartmentAndPositionEnum extends Enum
{
    public const DEPT_HIERARCHY = [
        'IT' => [
            'Senior Backend Developer',
            'Junior Backend Developer',
            'Senior Software Engineer',
            'Software Engineer',
            'DevOps',
            'Senior DevOps',
            'Product Manager',
            'IT Support'
        ],
        'Human Resource' => [
            'HR Staff',
            'HR Consultant',
            'Employee Relation',
            'Talent Acquisition'
        ],
        'Finance' => [
            'Head of Accounting',
            'Vice President of Finance analysis',
            'Staff Accountant',
            'Staff of Purchasing',
            'PPIC',
            'Supervisor',
            'Manager'
        ],
        'C Level' => [
            'CTO',
            'CEO',
            'CCO',
            'CMO',
            'CHR'
        ],
        'Sales' => [
            'Manager',
            'Team Lead',
            'Account Executive'
        ],
        'Operation' => [
            'Manager',
            'Supervisor',
            'Staff'
        ]
    ];
}
