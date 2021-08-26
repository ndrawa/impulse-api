<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends BaseModel
{
    protected $table = "questions";

    protected $fillable = [
        'test_id',
        'question',
    ];

    public function get_answer_for_test($question_id) {
        return Question::select('answer')
                ->where('id',$question_id);
    }

    public function tests() {
        return $this->belongsTo(Test::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }

    public function student_answers_essay() {
        return $this->hasMany(StudentEssayAnswer::class);
    }

    public function student_answers_multiple_choice() {
        return $this->hasMany(StudentMultipleChoiceAnswer::class);
    }
}
