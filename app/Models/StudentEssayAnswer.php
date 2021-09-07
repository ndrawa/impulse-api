<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEssayAnswer extends BaseModel
{
    protected $table = "student_answers_essay";

    protected $fillable = [
        'question_id',
        'answers',
        'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
