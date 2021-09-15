<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Course;
use App\Models\Room;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\ClassCourse;
use App\Models\StudentClassCourse;

use App\Http\Controllers\Api\V1\ClassCourse\ClassCourseController;

class BasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Role
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            Role::ROLE_LABORAN,
            Role::ROLE_STUDENT,
            Role::ROLE_ASPRAK,
            Role::ROLE_STAFF,
            Role::ROLE_ASLAB,
            Role::ROLE_DOSEN
        ];

        foreach($roles as $role) {
            Role::create(['name' => $role]);
        }

        //Staff
        $laboran = Staff::create([
            'nip' => 'laboran',
            'name' => 'Laboran (Super admin)',
            'code' => 'laboran'
        ]);
        $laboran->save();
        $user = $laboran->user;
        $user->assignRole(Role::ROLE_LABORAN);

        $admin = Staff::create([
            'nip' => 'admin1',
            'name' => 'admin1',
            'code' => 'admin1'
        ]);
        $admin->save();
        $user = $admin->user;
        $user->assignRole(Role::ROLE_LABORAN);
        $user->assignRole(Role::ROLE_DOSEN);

        $dosen_1 = Staff::create([
            'nip' => '99120385',
            'name' => 'ABDURRAHMAN JOKO SUSILO',
            'code' => 'AJS'
        ]);
        $user = $dosen_1->user;
        $user->assignRole(Role::ROLE_DOSEN);

        $dosen_2 = Staff::create([
            'nip' => '03790039',
            'name' => 'Dr. VERA SURYANI, S.T., M.T.',
            'code' => 'VRA'
        ]);
        $user = $dosen_2->user;
        $user->assignRole(Role::ROLE_DOSEN);

        $dosen_3 = Staff::create([
            'nip' => '03410341',
            'name' => 'AULIA ARIFWARDANA, S.Kom., M.T.',
            'code' => 'UIW'
        ]);
        $user = $dosen_3->user;
        $user->assignRole(Role::ROLE_DOSEN);

        $dosen_4 = Staff::create([
            'nip' => '18930093',
            'name' => 'ROSA RESKA RISKIANA',
            'code' => 'RSC'
        ]);
        $user = $dosen_4->user;
        $user->assignRole(Role::ROLE_DOSEN);

        $dosen_5 = Staff::create([
            'nip' => '18920120',
            'name' => 'JATI HILIAMSYAH HUSEN, S.T., M.Eng',
            'code' => 'JTI'
        ]);
        $user = $dosen_5->user;
        $user->assignRole(Role::ROLE_DOSEN);

        //Course/Mata kuliah
        $course_1 = Course::create([
            'code' => 'CII1F4',
            'name' => '	Algoritma Pemrograman',
        ]);
        $course_1->save();

        $course_2 = Course::create([
            'code' => 'CII2B4',
            'name' => '	Struktur Data',
        ]);
        $course_2->save();

        $course_3 = Course::create([
            'code' => 'CII2J4',
            'name' => 'Jaringan Komputer',
        ]);
        $course_3->save();

        $course_4 = Course::create([
            'code' => 'CII2H3',
            'name' => 'Sistem Operasi',
        ]);
        $course_4->save();

        $course_5 = Course::create([
            'code' => 'CII3B4',
            'name' => 'Pemrograman Berorientasi Objek',
        ]);
        $course_5->save();


        //Academic Year
        $academic_year_1 = AcademicYear::create([
            'year' => '2020',
            'semester' => 'odd',
        ]);

        $academic_year_2 = AcademicYear::create([
            'year' => '2020',
            'semester' => 'even',
        ]);

        $academic_year_3 = AcademicYear::create([
            'year' => '2021',
            'semester' => 'odd',
        ]);

        $academic_year_4 = AcademicYear::create([
            'year' => '2021',
            'semester' => 'even',
        ]);

        //Classroom/Kelas
        $classroom_1 = Classroom::create([
            'name' => 'IF-42-01',
        ]);

        $classroom_2 = Classroom::create([
            'name' => 'IF-42-02',
        ]);

        $classroom_3 = Classroom::create([
            'name' => 'IF-42-03',
        ]);

        $classroom_4 = Classroom::create([
            'name' => 'IF-42-04',
        ]);

        $classroom_5 = Classroom::create([
            'name' => 'IF-42-05',
        ]);

        //Room
        $room_1 = Room::create([
            'name' => 'LABIF-01 (dummy)',
            'desc' => 'Gedung A Lt. 2',
            'msteam_code' => 'PRPSL6',
            'msteam_link' => 'https://teams.microsoft.com/example'
        ]);

        $room_2 = Room::create([
            'name' => 'LABIF-02 (dummy)',
            'desc' => 'Gedung A Lt. 2',
            'msteam_code' => 'JIN60N',
            'msteam_link' => 'https://teams.microsoft.com/example'
        ]);

        $room_3 = Room::create([
            'name' => 'LABIF-03 (dummy)',
            'desc' => 'Gedung A Lt. 2',
            'msteam_code' => 'TU12K1',
            'msteam_link' => 'https://teams.microsoft.com/example'
        ]);

        //Class-Course
        $controller = new ClassCourseController();
        $request = new Request();
        //IF-42-01
        $request->replace([
            'class_id' => $classroom_1->id,
            'staff_id' => $dosen_2->id,
            'course_id' => $course_1->id,
            'academic_year_id' => $academic_year_1->id,
        ]);
        $class_course_1_1 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_1->id,
            'staff_id' => $dosen_5->id,
            'course_id' => $course_2->id,
            'academic_year_id' => $academic_year_1->id,
        ]);
        $class_course_1_2 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_1->id,
            'staff_id' => $dosen_3->id,
            'course_id' => $course_3->id,
            'academic_year_id' => $academic_year_2->id,
        ]);
        $class_course_1_3 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_1->id,
            'staff_id' => $dosen_1->id,
            'course_id' => $course_4->id,
            'academic_year_id' => $academic_year_2->id,
        ]);
        $class_course_1_4 = $controller->create_class_course($request);

        //IF-42-02
        $request->replace([
            'class_id' => $classroom_2->id,
            'staff_id' => $dosen_2->id,
            'course_id' => $course_1->id,
            'academic_year_id' => $academic_year_1->id,
        ]);
        $class_course_2_1 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_2->id,
            'staff_id' => $dosen_5->id,
            'course_id' => $course_2->id,
            'academic_year_id' => $academic_year_1->id,
        ]);

        $class_course_2_2 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_2->id,
            'staff_id' => $dosen_3->id,
            'course_id' => $course_3->id,
            'academic_year_id' => $academic_year_2->id,
        ]);
        $class_course_2_3 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_2->id,
            'staff_id' => $dosen_1->id,
            'course_id' => $course_4->id,
            'academic_year_id' => $academic_year_2->id,
        ]);
        $class_course_2_4 = $controller->create_class_course($request);

        //IF-42-03
        $request->replace([
            'class_id' => $classroom_3->id,
            'staff_id' => $dosen_2->id,
            'course_id' => $course_1->id,
            'academic_year_id' => $academic_year_1->id,
        ]);
        $class_course_3_1 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_3->id,
            'staff_id' => $dosen_4->id,
            'course_id' => $course_2->id,
            'academic_year_id' => $academic_year_1->id,
        ]);
        $class_course_3_2 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_3->id,
            'staff_id' => $dosen_3->id,
            'course_id' => $course_3->id,
            'academic_year_id' => $academic_year_2->id,
        ]);
        $class_course_3_3 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_3->id,
            'staff_id' => $dosen_4->id,
            'course_id' => $course_4->id,
            'academic_year_id' => $academic_year_2->id,
        ]);
        $class_course_3_4 = $controller->create_class_course($request);


        //ClassCourse tapi Kelas Ngulang
        $classroom_6 = Classroom::create([
            'name' => 'IF-41-10',
        ]);
        $classroom_7 = Classroom::create([
            'name' => 'IF-41-08',
        ]);

        $request->replace([
            'class_id' => $classroom_6->id,
            'staff_id' => $dosen_5->id,
            'course_id' => $course_5->id,
            'academic_year_id' => $academic_year_3->id,
        ]);
        $class_course_asal_1 = $controller->create_class_course($request);

        $request->replace([
            'class_id' => $classroom_7->id,
            'staff_id' => $dosen_2->id,
            'course_id' => $course_5->id,
            'academic_year_id' => $academic_year_3->id,
        ]);
        $class_course_asal_2 = $controller->create_class_course($request);


        //Student/mahasiswa/
        $student_1 = Student::create([
            'nim' => '1301181234',
            'name' => 'Jake',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_1->save();
        StudentClassCourse::create([
            'student_id' => $student_1->id,
            'class_course_id' => $class_course_1_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_1->id,
            'class_course_id' => $class_course_1_2->id,
        ]);

        $student_2 = Student::create([
            'nim' => '1301181236',
            'name' => 'Leo',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_2->save();
        StudentClassCourse::create([
            'student_id' => $student_2->id,
            'class_course_id' => $class_course_1_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_2->id,
            'class_course_id' => $class_course_1_2->id,
        ]);

        $student_3 = Student::create([
            'nim' => '1301181231',
            'name' => 'Nunung',
            'gender' => 'female',
            'religion' => 'katolik'
        ]);
        $student_3->save();
        StudentClassCourse::create([
            'student_id' => $student_3->id,
            'class_course_id' => $class_course_1_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_3->id,
            'class_course_id' => $class_course_1_2->id,
        ]);

        $student_4 = Student::create([
            'nim' => '1301181237',
            'name' => 'Lil Nas',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_4->save();
        StudentClassCourse::create([
            'student_id' => $student_4->id,
            'class_course_id' => $class_course_1_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_4->id,
            'class_course_id' => $class_course_1_2->id,
        ]);

        //
        $student_5 = Student::create([
            'nim' => '1301182190',
            'name' => 'Liam',
            'gender' => 'male',
            'religion' => 'protestan'
        ]);
        $student_5->save();
        StudentClassCourse::create([
            'student_id' => $student_5->id,
            'class_course_id' => $class_course_2_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_5->id,
            'class_course_id' => $class_course_2_2->id,
        ]);

        $student_6 = Student::create([
            'nim' => '1301182196',
            'name' => 'Lennon',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_6->save();
        StudentClassCourse::create([
            'student_id' => $student_6->id,
            'class_course_id' => $class_course_2_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_6->id,
            'class_course_id' => $class_course_2_2->id,
        ]);

        $student_7 = Student::create([
            'nim' => '1301182193',
            'name' => 'Tony McCaroll',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_7->save();
        StudentClassCourse::create([
            'student_id' => $student_7->id,
            'class_course_id' => $class_course_2_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_7->id,
            'class_course_id' => $class_course_2_2->id,
        ]);

        $student_8 = Student::create([
            'nim' => '1301182199',
            'name' => 'Giring',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_8->save();
        StudentClassCourse::create([
            'student_id' => $student_8->id,
            'class_course_id' => $class_course_2_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_8->id,
            'class_course_id' => $class_course_2_2->id,
        ]);

        //Ini mahasiswa ngulang ceritanya
        $student_9 = Student::create([
            'nim' => '1301185705',
            'name' => 'Doni Tandasial',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_9->save();
        StudentClassCourse::create([
            'student_id' => $student_9->id,
            'class_course_id' => $class_course_3_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_9->id,
            'class_course_id' => $class_course_asal_1->id,
        ]);

        $student_10 = Student::create([
            'nim' => '1301185702',
            'name' => 'Ridho Las Karbit',
            'gender' => 'male',
            'religion' => 'islam'
        ]);
        $student_10->save();
        StudentClassCourse::create([
            'student_id' => $student_10->id,
            'class_course_id' => $class_course_2_1->id,
        ]);
        StudentClassCourse::create([
            'student_id' => $student_10->id,
            'class_course_id' => $class_course_asal_2->id,
        ]);


    }
}
