<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum PermissionType: string
{
    case INDEX_USER = 'INDEX_USER';

    case SHOW_USER = 'SHOW_USER';

    case STORE_USER = 'STORE_USER';

    case UPDATE_USER = 'UPDATE_USER';

    case DELETE_USER = 'DELETE_USER';

    case SHOW_ROLE = 'SHOW_ROLE';

    case STORE_ROLE = 'STORE_ROLE';

    case DELETE_ROLE = 'DELETE_ROLE';

    case UPDATE_ROLE = 'UPDATE_ROLE';

    case INDEX_ROLE = 'INDEX_ROLE';

    case EDIT_ROLE_PERMISSION = 'EDIT_ROLE_PERMISSION';

    case SHOW_ROLE_PERMISSION = 'SHOW_ROLE_PERMISSION';

    case SHOW_USER_ROLE = 'SHOW_USER_ROLE';

    case EDIT_USER_ROLE = 'EDIT_USER_ROLE';

    case SHOW_PERMISSIONS = 'SHOW_PERMISSIONS';

    public static function getDescription($value): string
    {
        return Str::headline($value);
    }
}
