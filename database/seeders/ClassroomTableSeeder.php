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
        $classroom = Classroom::create([
            'name' => 'IF-43-01',
            'academic_year' => '2021',
            'semester' => 'old',
        ]);
        $classroom->save();


        $classroom = Classroom::create([
            'name' => 'IF-43-02',
            'academic_year' => '2021',
            'semester' => 'old',
        ]);
        $classroom = Classroom::create([
            'name' => 'IF-43-03',
            'academic_year' => '2021',
            'semester' => 'old',
        ]);
        $classroom->save();
    }
}
