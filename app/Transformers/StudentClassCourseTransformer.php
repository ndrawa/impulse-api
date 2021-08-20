<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\StudentClassCourse;

class StudentClassCourseTransformer extends TransformerAbstract
{
    public function transform(StudentClassCourse $studentclass)
    {
        return [
            'id' => $studentclass->id,
            'student' => $studentclass->student,
            'class_course' => [
                'id' => $studentclass->class_course->id,
                'class' => $studentclass->class_course->classes,
                'staff' => $studentclass->class_course->staffs,
                'course' => $studentclass->class_course->courses,
                'academic_year' => $studentclass->class_course->academic_years,
            ]

        ];
    }
}
