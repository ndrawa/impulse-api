<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;

class TestTransformer extends TransformerAbstract
{
    public function transform(Test $test)
    {
        $data = [];
        $data['test'] = [
            'id' => $test->id,
            'type' => $test->type,
            'created_at' => $test->created_at,
            'updated_at' => $test->updated_at,
        ];

        $data['question'] = $test->questions;

        foreach($data['question'] as $key=>$q) {
            $question = $data['question'][$key];
            $data['question'][$key]['answers'] = $question->answers;
        }
        return $data;
    }
}
