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
        'student_id',
        'schedule_test_id',
        'question_id',
        'grade',
        'asprak_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function asprak()
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule_test()
    {
        return $this->hasMany(ScheduleTest::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
