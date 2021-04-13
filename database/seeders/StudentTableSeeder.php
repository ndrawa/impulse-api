<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Role;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $student = Student::create([
            'nim' => '1301184327',
            'name' => 'Fariz M R',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student->save();
        // assign role
        $user = $student->user;
        $user->assignRole(Role::ROLE_ASPRAK);

        $student = Student::create([
            'nim' => '1301184366',
            'name' => 'Indra W',
            'gender' => 'male',
            'religion' => 'hindu'
        ]);
        $student->save();

        $student = Student::create([
            'nim' => '1301180174',
            'name' => 'Dani Andhika P',
            'gender' => 'male',
            'religion' => 'katolik'
        ]);
        $student->save();

        $student = Student::create([
            'nim' => '1301184346',
            'name' => 'Putri Ester Sumolang',
            'gender' => 'female',
            'religion' => 'protestan'
        ]);
        $student->save();
    }
}
