<?php

namespace Database\Seeders;

use App\Models\StudentClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Students_ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $student_id =  DB::table('students')
                    ->select('id')
                    ->where('nim', '1301184327')
                    ->first();
        $class_id = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-03')
                    ->first();

        $student_class = StudentClass::create([
            'student_id' => $student_id->id,
            'class_id' => $class_id->id,
        ]);
        $student_class->save();

        $student_id =  DB::table('students')
                    ->select('id')
                    ->where('nim', '1301180174')
                    ->first();
        $class_id = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-03')
                    ->first();

        $student_class = StudentClass::create([
            'student_id' => $student_id->id,
            'class_id' => $class_id->id,
        ]);
        $student_class->save();
    }
}
