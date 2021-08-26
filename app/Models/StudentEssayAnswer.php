<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEssayAnswer extends BaseModel
{
    protected $table = "student_answers_essay";

    protected $fillable = [
        'question_id',
        'answers',
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
}
