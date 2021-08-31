<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends BaseModel
{
    protected $table = "grade";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'schedule_test_id',
        'grade_tp',
        'grade_pretest',
        'grade_jurnal',
        'grade_posttest',
        'grade'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule_test()
    {
        return $this->hasOne(ScheduleTest::class);
    }
}
