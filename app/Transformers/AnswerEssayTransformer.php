<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\StudentEssayAnswer;

class AnswerEssayTransformer extends TransformerAbstract
{
    public function transform(StudentEssayAnswer $answer)
    {
        return [
            'id' => $answer->id,
            'question' => $answer->question,
            'answers' => $answer->answers,
            'user_id' => $answer->user->student,
        ];
    }
}
