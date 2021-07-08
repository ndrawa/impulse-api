<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends BaseModel
{
    protected $table = "tests";

    protected $fillable = [
        'type'
    ];

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function schedule_test()
    {
        return $this->hasMany(ScheduleTest::class);
    }
}
