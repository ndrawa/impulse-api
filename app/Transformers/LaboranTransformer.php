<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Staff;

class LaboranTransformer extends TransformerAbstract
{
    public function transform(Student $student, Classroom $classroom, Course $course, Staff $staff)
    {
        return [
            'id' => $student->id,
            'name' => $student->name,
            'nim' => $student->nim,
            'gender' => $student->gender,
            'religion' => $student->religion,
            'classname' => $classroom->name,
            'academic_year' => $classroom->academic_year,
            'semester' => $classroom->semester,
            'coursename' => $course->name,
            'staffname' => $staff->name,
            'nip' => $staff->nip,
            'code' => $staff->code,
        ];
    }
}
