<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staff_id = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'AJS')
                    ->first();
        $course_id = DB::table('courses')
                     ->select('id')
                     ->where('code', 'CII2J4')
                     ->first();

        $classroom = Classroom::create([
            'name' => 'IF-43-03',
            'staff_id' => $staff_id->id,
            'course_id' => $course_id->id,
            'academic_year' => '2020/2021',
            'semester' => '5',
        ]);
        $classroom->save();
    }
}
