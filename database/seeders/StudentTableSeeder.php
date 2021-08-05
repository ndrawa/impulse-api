<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Asprak;
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
        $staff_1 = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'AJS')
                    ->first();
        $class_1 = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-01')
                    ->first();
        $academic_year_1 = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'odd')
                            ->first();
        $course_1 =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CSG2H3')
                        ->first();
        $class_course_1 =  DB::table('class_course')
                            ->select('id')
                            ->where('class_id', $class_1->id)
                            ->where('academic_year_id', $academic_year_1->id)
                            ->where('course_id', $course_1->id)
                            ->where('staff_id', $staff_1->id)
                            ->first();

        $staff_2 = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'AJS')
                    ->first();
        $class_2 = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-02')
                    ->first();
        $academic_year_2 = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'odd')
                            ->first();
        $course_2 =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CSG2H3')
                        ->first();
        $class_course_2 =  DB::table('class_course')
                            ->select('id')
                            ->where('class_id', $class_2->id)
                            ->where('academic_year_id', $academic_year_2->id)
                            ->where('course_id', $course_2->id)
                            ->where('staff_id', $staff_2->id)
                            ->first();

        $staff_3 = DB::table('staffs')
                            ->select('id')
                            ->where('code', 'UIW')
                            ->first();
        $class_3 = DB::table('classes')
                            ->select('id')
                            ->where('name', 'IF-43-01')
                            ->first();
        $academic_year_3 = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'even')
                            ->first();
        $course_3 =    DB::table('courses')
                            ->select('id')
                            ->where('code', 'CII2J4')
                            ->first();
        $class_course_3 =  DB::table('class_course')
                            ->select('id')
                            ->where('class_id', $class_3->id)
                            ->where('academic_year_id', $academic_year_3->id)
                            ->where('course_id', $course_3->id)
                            ->where('staff_id', $staff_3->id)
                            ->first();


        $admin = Student::create([
            'nim' => 'admin2',
            'name' => 'admin2',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $admin->save();
        // assign role
        $user = $admin->user;
        $user->assignRole(Role::ROLE_ASPRAK);
        $user->assignRole(Role::ROLE_ASLAB);

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
        $user->assignRole(Role::ROLE_ASPRAK);
        $user->assignRole(Role::ROLE_ASLAB);


        //REAL ASPRAK
        $asprak_1 = Student::create([
            'nim' => '1301162222',
            'name' => 'Aku Asprak 1',
            'gender' => 'male',
            'religion' => 'islam',
        ]);
        $asprak_1->save();
        $user = $asprak_1->user;
        $user->assignRole(Role::ROLE_ASPRAK);
        $asp_1 = Asprak::create([
            'student_id' => $asprak_1->id,
            'class_course_id' => $class_course_1->id,
        ]);
        $asp_1->save();
        $asp_2 = Asprak::create([
            'student_id' => $asprak_1->id,
            'class_course_id' => $class_course_2->id,
        ]);
        $asp_2->save();

        $asprak_2 = Student::create([
            'nim' => '1301161001',
            'name' => 'Aku Asprak 2',
            'gender' => 'female',
            'religion' => 'hindu',
        ]);
        $asprak_2->save();
        $user = $asprak_2->user;
        $user->assignRole(Role::ROLE_ASPRAK);
        $asp_1 = Asprak::create([
            'student_id' => $asprak_1->id,
            'class_course_id' => $class_course_1->id,
        ]);
        $asp_1->save();
        $asp_2 = Asprak::create([
            'student_id' => $asprak_1->id,
            'class_course_id' => $class_course_2->id,
        ]);
        $asp_2->save();

        $asprak_3 = Student::create([
            'nim' => '1301163387',
            'name' => 'Aku Asprak 3',
            'gender' => 'male',
            'religion' => 'katolik',
        ]);
        $asprak_3->save();
        $user = $asprak_3->user;
        $user->assignRole(Role::ROLE_ASPRAK);
        $asp_1 = Asprak::create([
            'student_id' => $asprak_3->id,
            'class_course_id' => $class_course_3->id,
        ]);
        $asp_1->save();

    }
}
