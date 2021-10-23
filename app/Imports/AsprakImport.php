<?php

namespace App\Imports;

use App\Models\Asprak;
use App\Models\ClassCourse;
use App\Models\Course;
use App\Models\Student;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AsprakImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) 
        {
            if($key < 1 ) continue;
            $student = Student::where('nim', $row[1])->first();
            $course = Course::where('code', $row[2])->first();
            $class_course = ClassCourse::where('course_id', $course->id)->first();
            $user = User::find($student->user_id);
            if (($student != null && $class_course != null) && 
            (Asprak::where('student_id', $student->id)
            ->where('class_course_id', $class_course->id)->first() == null)) {
                $asprak = Asprak::create([
                    'student_id' => $student->id,
                    'asprak_code' => $row[0],
                    'class_course_id' => $class_course->id
                ]);
                $asprak->save();
                $user->assignRole(Role::ROLE_ASPRAK);
            }
        }
    }
}

