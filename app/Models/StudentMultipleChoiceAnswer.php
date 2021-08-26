<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMultipleChoiceAnswer extends BaseModel
{
    protected $table = "student_answers_multiple_choice";

    protected $fillable = [
        'question_id',
        'answer_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
