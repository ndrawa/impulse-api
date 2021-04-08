<?php

namespace App\Models;

use App\Traits\HasULID;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasULID;
}