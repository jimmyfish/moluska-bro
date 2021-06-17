<?php


namespace App\Http\Enum;


use MyCLabs\Enum\Enum;

class RoleEnum extends Enum
{
    public const ROLES = [
        'Super Admin',
        'Admin',
        'Guest',
    ];
}
