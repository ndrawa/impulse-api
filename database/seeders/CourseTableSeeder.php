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
            'code' => 'JRK',
            'name' => 'JARINGAN KOMPUTER',
        ]);
        $course->save();
        $course = Course::create([
            'code' => 'PBO',
            'name' => 'PEMROGRAMAN BERBASIS OBJEK',
        ]);
        $course->save();
    }
}
