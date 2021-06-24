<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        function replace_gender($data)
        {
            if ($data == 'PRIA') {
                return 'male';
            } else {
                return 'female';
            }
        }

        function replace_religion($data)
        {
            if ($data == 'ISLAM') {
                return 'islam';
            } elseif ($data == 'PROTESTAN') {
                return 'protestan';
            } elseif ($data == 'KATOLIK') {
                return 'katolik';
            } elseif ($data == 'BUDDHA') {
                return 'buddha';
            } elseif ($data == 'HINDU') {
                return 'hindu';
            } elseif ($data == 'KHONGHUCU') {
                return 'khonghucu';
            } elseif ($data == 'KRISTEN') {
                return 'kristen';
            }
        }

        foreach ($collection as $key => $row) 
        {
            if ($row[0] != null) {
                if($key < 1 ) continue;
                if (Student::where('nim', $row[0])->first() == null) 
                {
                    Student::create([
                        'name' => $row[1],
                        'nim' => $row[0],
                        'gender' => replace_gender($row[4]),
                        'religion' => replace_religion($row[5]),
                    ]);
                }

                if (Student::where('nim', $row[0])->first() != null) 
                {
                    if (Course::where('code', $row[8])->first() == null) 
                    {
                        $course = Course::create([
                            'name' => $row[9],
                            'code' => $row[8]
                        ]);
                    }

                    if (Classroom::where('name', $row[2])->where('course_id', Course::where('code', $row[8])->first()->id)->first() == null) 
                    {
                        $staff_id = Staff::where('code', $row[10])->first();
                        $course_id = Course::where('code', $row[8])->first();
                        $classroom = Classroom::create([
                            'staff_id' => $staff_id->id,
                            'name' => $row[2],
                            'course_id' => $course_id->id,
                            'academic_year' => $row[11],
                            'semester' => $row[12],
                        ]);
                        $classroom->save();
                    }   
                    
                    $student_id = Student::where('nim', $row[0])->first();
                    $class_id = Classroom::where('name', $row[2])->where('course_id', Course::where('code', $row[8])->first()->id)->first();
                    if (StudentClass::where(['student_id' => $student_id->id, 'class_id' => $class_id->id])->first() == null) 
                    {
                        StudentClass::create([
                            'student_id' => $student_id->id,
                            'class_id' => $class_id->id,
                        ]);
                    }
                }
            }
        }
    }
}
