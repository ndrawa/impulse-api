<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Classroom;
use App\Models\ClassCourse;
use App\Models\StudentClassCourse;
use App\Models\AcademicYear;
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

        function replace_semester($data)
        {
            if ($data % 2 == 0){
                return 'even';
            } else {
                return 'odd';
            }
        }

        foreach ($collection as $key => $row) 
        {
            if ($row[0] != null) {
                if($key < 1 ) continue;
                if (Student::where('nim', $row[0])->first() == null) 
                {
                    // create Student
                    Student::create([
                        'name' => $row[1],
                        'nim' => $row[0],
                        'gender' => replace_gender($row[4]),
                        'religion' => replace_religion($row[5]),
                    ]);
                }

                if (Student::where('nim', $row[0])->first() != null) 
                {
                    // create Courses
                    if (Course::where('code', $row[8])->first() == null) 
                    {
                        $course = Course::create([
                            'name' => $row[9],
                            'code' => $row[8]
                        ]);
                    }

                    // create Academic Years
                    $semester = '';
                    if ($row[12] % 2 == 0){
                        $semester = 'even';
                    } else {
                        $semester = 'odd';
                    }
                    if (AcademicYear::where('year', $row[11])
                        ->where('semester', $semester)->first() == null) {
                        $academic_year = AcademicYear::create([
                            'year' => $row[11],
                            'semester' => replace_semester($row[12])
                        ]);
                    }

                    // create Classes
                    if (Classroom::where('name', $row[2])
                        ->where('academic_year', $row[11])
                        ->where('semester', $row[12])->first() == null) {
                        $classroom = Classroom::create([
                            'name' => $row[2],
                            'academic_year' => $row[11],
                            'semester' => $row[12]
                        ]);
                        $classroom->save();
                    }

                    // create ClassCourse
                    $fclass_id = Classroom::where('name', $row[2])->first()->id;
                    $fcourse_id = Course::where('code', $row[8])->first()->id;
                    $fstaff_id = Staff::where('code', $row[10])->first()->id;
                    $semester = '';
                    if ((int)$row[2] % 2 == 0){
                        $semester = 'even';
                    } else {
                        $semester = 'odd';
                    }
                    $facademic_year_id = AcademicYear::where('year', $row[11])
                                        ->where('semester', $semester)->first()->id;
                    if (ClassCourse::where('class_id', $fclass_id)
                        ->where('course_id', $fcourse_id)
                        ->where('staff_id', $fstaff_id)
                        ->where('academic_year_id', $facademic_year_id)
                        ->first() == null) {
                        $classcourse = ClassCourse::create([
                            'class_id' => $fclass_id,
                            'course_id' => $fcourse_id,
                            'staff_id' => $fstaff_id,
                            'academic_year_id' => $facademic_year_id
                        ]);
                        $classcourse->save();
                    }

                    // create Student classes
                    $student_id = Student::where('nim', $row[0])->first()->id;
                    $class_course_id = ClassCourse::where('class_id', $fclass_id)
                            ->where('course_id', $fcourse_id)
                            ->where('staff_id', $fstaff_id)
                            ->where('academic_year_id', $facademic_year_id)
                            ->first()->id;
                    if(StudentClassCourse::where('student_id', $student_id)->where('class_course_id', $class_course_id)->first() == null) {
                    $student_class = StudentClassCourse::create([
                        'student_id' => $student_id,
                        'class_course_id' => $class_course_id,
                    ]);
                    }
                }
            }
        }
    }
}
