<?php

namespace Database\Seeders;
use App\Models\ClassCourse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ClassCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $class_id = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-03')
                    ->first();
        $academic_year_id = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'even')
                            ->first();
        $course_id =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CII2J4')
                        ->first();

        $class_course = ClassCourse::create([
            'class_id' => $class_id->id,
            'course_id' => $course_id->id,
            'academic_year_id' => $academic_year_id->id,
        ]);
    }
}
