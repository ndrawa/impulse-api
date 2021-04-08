<?php

namespace App\Models;

use App\Traits\HasULID;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasULID;

    const ROLE_ADMIN = "admin";
    const ROLE_STUDENT = "student";
    const ROLE_ASPRAK = "asprak";
    const ROLE_STAFF = "staff";
}