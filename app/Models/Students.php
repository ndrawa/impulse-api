<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Students extends User
{
    protected $fillable = [
        'nim', 'name', 'gender', 'religion', 'user_id', 'created_at', 'updated_at',
    ];
}
