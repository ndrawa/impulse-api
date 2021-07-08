<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleTest extends BaseModel
{
    protected $fillable = [
        'schedule_id',
        'test_id',
        'time_start',
        'time_end',
        'is_active',
        'auth',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
