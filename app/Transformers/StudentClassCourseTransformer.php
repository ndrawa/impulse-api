<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\StudentClassCourse;

class StudentClassCourseTransformer extends TransformerAbstract
{
    public function transform(StudentClassCourse $studentclass)
    {
        return [
            // 'id' => $studentclass->id,
            'student_id' => $studentclass->student_id,
            'class_course_id' => $studentclass->class_course_id
        ];
    }
}
