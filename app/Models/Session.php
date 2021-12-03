<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends BaseModel
{
    protected $table = "sessions";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'user_agent',
        'login_at',
        'expired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
