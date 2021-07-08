<?php

namespace Database\Seeders;
use App\Models\AcademicYear;

use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $academic_year_1 = AcademicYear::create([
            'year' => '2020',
            'semester' => 'odd',
        ]);

        $academic_year_2 = AcademicYear::create([
            'year' => '2020',
            'semester' => 'even',
        ]);
    }
}
