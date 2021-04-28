<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends BaseModel
{
    protected $table = "rooms";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'desc',
        'msteam_code',
        'msteam_link',
    ];
}
