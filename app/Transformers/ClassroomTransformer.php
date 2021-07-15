<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Classroom;

class ClassroomTransformer extends TransformerAbstract
{
    public function transform(Classroom $classroom)
    {
        return [
            'id' => $classroom->id,
            'staff_id' => $classroom->staff_id,
            'name' => $classroom->name,
            'academic_year' => $classroom->academic_year,
            'semester' => $classroom->semester,
        ];
    }
}


