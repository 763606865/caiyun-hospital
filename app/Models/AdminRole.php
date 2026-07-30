<?php

namespace App\Models;

use Spatie\Permission\Models\Role;

class AdminRole extends Role
{
    protected $attributes = [
        'guard_name' => 'admin',
    ];
}
