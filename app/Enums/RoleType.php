<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum RoleType: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';

    case DATABASE = 'DATABASE';

    case OPERATION = 'OPERATION';

    public static function getDescription($value): string
    {
        return Str::headline($value);
    }
}
