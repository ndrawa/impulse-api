<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Course;

class CourseTransformer extends TransformerAbstract
{
    public function transform(Course $course)
    {
        return [
            'id' => $course->id,
            'code' => $course->code,
            'name' => $course->name,
        ];
    }
}


