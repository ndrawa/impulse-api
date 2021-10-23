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
            'asprak_code' => $acc->asprak_code,
            'student' => $acc->student,
            'class_course' => [
                'classes' => $acc->class_course->classes,
                'courses' => $acc->class_course->courses,
                'academic_years' => $acc->class_course->academic_years,
                'staffs' => $acc->class_course->staffs,
            ]
        ];
    }
}


