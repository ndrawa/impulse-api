<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\StudentClasses;

class StudentClassesTransformer extends TransformerAbstract
{
    public function transform(StudentClasses $studentclasses)
    {
        return [
            'student_id' => $studentclasses->name,
            'class_id' => $studentclasses->nim
        ];
    }
}
