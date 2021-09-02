<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class LaboranTransformer extends TransformerAbstract
{
    public function transform($student)
    {
        return [
            // 'id' => $student->id,students_class_course_id
            'student_id' => $student->student_id,
            'nim' => $student->nim,
            'name' => $student->name,
            'students_class_course_id' => $student->students_class_course_id,
            'class_id' => $student->class_id,
            'class_name' => $student->class_name,
            'gender' => $student->gender,
            'religion' => $student->religion,
            'course_code' => $student->course_code,
            'course_name' => $student->course_name,
            'staff_code' => $student->staff_code,
            'academic_year' => $student->year,
            'semester' => $student->semester,
        ];
    }
}
