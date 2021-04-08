<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Student;

class StudentTransformer extends TransformerAbstract
{
    public function transform(Student $student)
    {
        return [
            'id' => $student->id,
            'name' => $student->name,
            'nim' => $student->nim,
            'gender' => $student->gender,
            'religion' => $student->religion
        ];
    }
}
