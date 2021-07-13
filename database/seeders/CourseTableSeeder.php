<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $course = Course::create([
            'code' => 'CII2J4',
            'name' => 'JARINGAN KOMPUTER',
        ]);
        $course->save();
        $course = Course::create([
            'code' => 'CSG2H3',
            'name' => 'PEMROGRAMAN BERBASIS OBJEK',
        ]);
        $course->save();
        $course = Course::create([
            'code' => 'CII1F4',
            'name' => 'ALGORITMA PEMROGRAMAN',
        ]);
        $course->save();
        $course = Course::create([
            'code' => 'CII2B4',
            'name' => 'STRUKTUR DATA',
        ]);
        $course->save();
    }
}
