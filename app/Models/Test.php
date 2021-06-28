<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends BaseModel
{
    protected $table = "tests";

    protected $fillable = [
        'time_start',
        'time_end',
    ];

    public function questions() {
        return $this->hasMany(Question::class);
    }
}
