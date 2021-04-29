<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class LaboranTransformer extends TransformerAbstract
{
    public function transform($student)
    {
        return [
            'nim' => $student->nim,
            'name' => $student->name,
            'class_name' => $student->class_name,
            'gender' => $student->gender,
            'religion' => $student->religion,
            'course_code' => $student->course_code,
            'course_name' => $student->course_name,
            'staff_code' => $student->staff_code,
            'academic_year' => $student->academic_year,
            'semester' => $student->semester,
        ];
    }
}
