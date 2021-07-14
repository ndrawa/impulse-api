<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ClassCourse;

class ClassCourseTransformer extends TransformerAbstract
{
    public function transform(ClassCourse $class_course)
    {
        $data = [
            'id' => $class_course->id,
            'class_id' => $class_course->class_id,
            'course_id' => $class_course->course_id,
            'academic_year_id' => $class_course->academic_year_id,
        ];

        return $data;
    }
}


