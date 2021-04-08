<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasULID;

class BaseModel extends Model
{
    use HasULID;
}