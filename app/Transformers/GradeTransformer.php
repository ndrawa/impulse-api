<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Grade;

class GradeTransformer extends TransformerAbstract
{
    public function transform(Grade $grade)
    {
        return [
            'id' => $grade->id,
            'student_id' => $grade->student_id,
            'question' => [
                'id' => $grade->question->id,
                'text' => $grade->question->question,
                'answers' => $grade->question->answers,
            ],
            'grade' => $grade->grade,
            'asprak' => $grade->asprak,
        ];
    }
}
