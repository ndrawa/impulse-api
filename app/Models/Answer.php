<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends BaseModel
{
    protected $fillable = [
        'question_id',
        'answer',
        'is_answer',
    ];

    public function question() {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function studient_answers_multiple_choice() {
        return $this->hasMany(StudentMultipleChoiceAnswer::class);
    }
}
