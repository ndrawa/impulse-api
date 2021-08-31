<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Question;

class QuestionTransformer extends TransformerAbstract
{
    public function transform(Question $question)
    {
        $data['question'] = [
            'type'  => $question->type,
            'question' => $question->question,
            'weight' => $question->weight,
        ];

        $transformer = new AnswerTransformer;
        $data['answer'] = $transformer->transform($question->answers);

        return $data;
    }
}
