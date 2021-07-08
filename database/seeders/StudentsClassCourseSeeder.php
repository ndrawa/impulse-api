<?php

namespace Database\Seeders;
use App\Models\StudentClassCourse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StudentsClassCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $class_course_id =  DB::table('class_course')
                            ->select('id')
                            ->get()
                            ->first();

        $student_id =   DB::table('students')
                        ->select('id')
                        ->where('nim', '1301184366')
                        ->first();

        $student_class_course = StudentClassCourse::create([
            'student_id' => $student_id->id,
            'class_course_id' => $class_course_id->id,
        ]);
        //
        $student_id =   DB::table('students')
                        ->select('id')
                        ->where('nim', '1301180174')
                        ->first();
        $student_class_course = StudentClassCourse::create([
            'student_id' => $student_id->id,
            'class_course_id' => $class_course_id->id,
        ]);
    }
}
