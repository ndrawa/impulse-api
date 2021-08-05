<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(StaffTableSeeder::class);
        $this->call(CourseTableSeeder::class);
        $this->call(RoomTableSeeder::class);
        $this->call(ClassroomTableSeeder::class);
        $this->call(AcademicYearSeeder::class);
        $this->call(ClassCourseSeeder::class);
        $this->call(StudentTableSeeder::class);
        $this->call(StudentsClassCourseSeeder::class);
        $this->call(ScheduleTableSeeder::class);
    }
}
