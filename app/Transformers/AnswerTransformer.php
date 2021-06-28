<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Answer;

class AnswerTransformer extends TransformerAbstract
{
    public function transform(Annswer $answer)
    {
        return [
            'id' => $answer->id,
            'answer' => $answer->answer,
            'is_answer' => $answer->is_answer,
        ];
    }
}
