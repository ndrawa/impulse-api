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

    public function questions() {
        return $this->belongsTo(Question::class);
    }
}