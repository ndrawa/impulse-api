<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\StudentClass;

class StudentClassTransformer extends TransformerAbstract
{
    public function transform(StudentClass $studentclass)
    {
        return [
            'id' => $studentclass->id,
            'student_id' => $studentclass->student_id,
            'class_id' => $studentclass->class_id
        ];
    }
}
