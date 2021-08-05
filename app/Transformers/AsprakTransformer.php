<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Asprak;

class AsprakTransformer extends TransformerAbstract
{
    public function transform(Asprak $acc)
    {
        return [
            'id' => $acc->id,
            // 'student_id' => $acc->student_id,
            'student' => $acc->student,
            'class_course_id' => $acc->class_course_id,
        ];
    }
}


