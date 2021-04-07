<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffs extends User
{
    protected $fillable = [
        'nip', 'name', 'code', 'created_at', 'updated_at',
    ];
}
