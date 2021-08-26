<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\StudentMultipleChoiceAnswer;

class AnswerMultipleChoiceTransformer extends TransformerAbstract
{
    public function transform(StudentMultipleChoiceAnswer $answer)
    {
        return [
            'id' => $answer->id,
            'question_id' => $answer->question,
            'answer_id' => $answer->answer,
            'user_id' => $answer->user->student,
        ];
    }
}
