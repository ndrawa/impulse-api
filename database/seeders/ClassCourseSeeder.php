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
        $staff = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'AJS')
                    ->first();
        $class = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-01')
                    ->first();
        $academic_year = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'odd')
                            ->first();
        $course =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CSG2H3')
                        ->first();

        $class_course = ClassCourse::create([
            'class_id' => $class->id,
            'staff_id' => $staff->id,
            'course_id' => $course->id,
            'academic_year_id' => $academic_year->id,
        ]);

        $staff = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'AJS')
                    ->first();
        $class = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-02')
                    ->first();
        $academic_year = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'odd')
                            ->first();
        $course =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CSG2H3')
                        ->first();

        $class_course = ClassCourse::create([
            'class_id' => $class->id,
            'staff_id' => $staff->id,
            'course_id' => $course->id,
            'academic_year_id' => $academic_year->id,
        ]);

        $staff = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'VRA')
                    ->first();
        $class = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-01')
                    ->first();
        $academic_year = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'even')
                            ->first();
        $course =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CII2B4')
                        ->first();
        $class_course = ClassCourse::create([
            'class_id' => $class->id,
            'staff_id' => $staff->id,
            'course_id' => $course->id,
            'academic_year_id' => $academic_year->id,
        ]);

        $staff = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'UIW')
                    ->first();
        $class = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-01')
                    ->first();
        $academic_year = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'even')
                            ->first();
        $course =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CII2J4')
                        ->first();
        $class_course = ClassCourse::create([
            'class_id' => $class->id,
            'staff_id' => $staff->id,
            'course_id' => $course->id,
            'academic_year_id' => $academic_year->id,
        ]);

        $staff = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'UIW')
                    ->first();
        $class = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-03')
                    ->first();
        $academic_year = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'even')
                            ->first();
        $course =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CII2J4')
                        ->first();
        $class_course = ClassCourse::create([
            'class_id' => $class->id,
            'staff_id' => $staff->id,
            'course_id' => $course->id,
            'academic_year_id' => $academic_year->id,
        ]);
    }
}
