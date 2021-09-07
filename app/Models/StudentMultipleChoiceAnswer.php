<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMultipleChoiceAnswer extends BaseModel
{
    protected $table = "student_answers_multiple_choice";

    protected $fillable = [
        'question_id',
        'answer_id',
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

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
}
