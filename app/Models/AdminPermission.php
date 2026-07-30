<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class AdminPermission extends Permission
{
    protected $attributes = [
        'guard_name' => 'admin',
    ];
}
