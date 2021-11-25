<?php

namespace App\Http\Controllers\Api\V1\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\AcademicYear;
use App\Models\Asprak;
use App\Models\Classroom;
use App\Models\ClassCourse;
use App\Models\Course;
use App\Models\Module;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentClassCourse;
use App\Models\Staff;
use App\Models\User;
use App\Models\StudentPresence;
use App\Models\AsprakPresence;
use App\Models\Bap;
use App\Transformers\AsprakTransformer;
use App\Transformers\LaboranTransformer;
use App\Transformers\ScheduleTransformer;
use App\Transformers\StudentTransformer;
use App\Transformers\StudentClassCourseTransformer;
use App\Transformers\ClassTransformer;
use App\Transformers\ClassCourseTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Imports\StudentImport;
use App\Imports\AsprakImport;
// use App\Imports\StudentClassImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LaboranController extends BaseController
{
    public function index(Request $request)
    {
        $users = DB::table('students')
            ->join('students_class_course', 'students.id', '=', 'students_class_course.student_id')
            ->join('class_course', 'class_course.id', '=', 'students_class_course.class_course_id')
            ->join('classes', 'classes.id', '=', 'class_course.class_id')
            ->join('courses', 'courses.id', '=', 'class_course.course_id')
            ->join('staffs', 'staffs.id', '=', 'class_course.staff_id')
            ->join('academic_years', 'academic_years.id', '=', 'class_course.academic_year_id')
            ->select('students.id as student_id', 'students.nim', 'students.name', 'students_class_course.id as students_class_course_id',
            'classes.id as class_id', 'classes.name as class_name', 'students.gender',
            'students.religion', 'courses.code as course_code', 'courses.name as course_name',
            'staffs.code as staff_code', 'academic_years.year', 'academic_years.semester');
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $kelas = "";
        $course = "";
        if($request->has('kelas') && !empty($request->kelas)) {
            $kelas = $request->get('kelas');
        }

        if($request->has('course') && !empty($request->course)) {
            $course = $request->get('course');
        }

        if ($request->has('search')){
            $request->whenHas('search', function($search) use (&$users, &$kelas, &$course) {
                $users = $users->where('classes.name', 'ILIKE', '%'.$kelas.'%')
                                ->where('courses.name', 'ILIKE', '%'.$course.'%')
                                ->where(function ($query) use (&$search) {
                                    $query->where('students.name', 'ILIKE', '%'.$search.'%')
                                            ->orWhere('students.nim', 'ILIKE', '%'.$search.'%')
                                            ->orWhere('classes.name', 'ILIKE', '%'.$search.'%')
                                            ->orWhere('courses.name', 'ILIKE', '%'.$search.'%')
                                            ->orWhere('courses.code', 'ILIKE', '%'.$search.'%')
                                            ->orWhere('staffs.code', 'ILIKE', '%'.$search.'%')
                                            ->orWhere('academic_years.year', 'ILIKE', '%'.$search.'%');
                                    });
                                // ->orWhere('students.gender', 'ILIKE', '%'.$search.'%')
                                // ->orWhere('students.religion', 'ILIKE', '%'.$search.'%')
            });
        }
        else{
            $users = $users->where('classes.name', 'ILIKE', '%'.$kelas.'%')
                            ->where('courses.name', 'ILIKE', '%'.$course.'%');
        }

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $users->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $users->orderBy($orderBy);
        }

        $users = $users->paginate($per_page);

        return $this->response->paginator($users, new LaboranTransformer);
    }

    public function show(Request $request, $id)
    {
        $users = DB::table('students')
        ->join('students_class_course', 'students.id', '=', 'students_class_course.student_id')
            ->join('class_course', 'class_course.id', '=', 'students_class_course.class_course_id')
            ->join('classes', 'classes.id', '=', 'class_course.class_id')
            ->join('courses', 'courses.id', '=', 'class_course.course_id')
            ->join('staffs', 'staffs.id', '=', 'class_course.staff_id')
            ->join('academic_years', 'academic_years.id', '=', 'class_course.academic_year_id')
            ->select('students.id as student_id', 'students.nim', 'students.name', 'classes.id as class_id', 'classes.name as class_name', 'students.gender',
            'students.religion', 'courses.code as course_code', 'courses.name as course_name',
            'staffs.code as staff_code', 'academic_years.year')
            ->where('students.id', '=', $id)
            ->get();
        return $this->response->item($users, new LaboranTransformer);
    }


    public function create(Request $request)
    {
        $this->authorize('create', Student::class);
        $this->validate($request, [
            'nim' => [
                'required',
            ],
            'name' => 'required',
            'gender' => [
                'required'
            ],
            'religion' => [
                'required'
            ],
            'class_name' => 'required',
            'course_code' => 'required',
            'course_name' => 'required',
            'staff_code' => 'required',
            'academic_year' => 'required',
            'semester' => 'required'
        ]);

        // create Student
        if (Student::where('nim', $request->nim)->first() == null) {
            $student = Student::create([
                'name' => $request->name,
                'nim' => $request->nim,
                'gender' => $request->gender,
                'religion' => $request->religion
            ]);
        }

        // create Courses
        if (Course::where('code', $request->course_code)->first() == null) {
            $course = Course::create([
                'name' => $request->course_name,
                'code' => $request->course_code
            ]);
        }

        // create Academic Years
        $semester = '';
        if (is_numeric($request->semester)){
            if ($request->semester % 2 == 0){
                $semester = 'even';
            } else {
                $semester = 'odd';
            }
        } else {
            $semester = $request->semester;
        }
        if (AcademicYear::where('year', $request->academic_year)
            ->where('semester', $semester)->first() == null) {
            $academic_year = AcademicYear::create([
                'year' => $request->academic_year,
                'semester' => $semester
            ]);
        }

        // create Classes
        if (Classroom::where('name', $request->class_name)->first() == null) {
            $classes = Classroom::create([
                'name' => $request->class_name
            ]);
        }

        // create ClassCourse
        $fclass_id = Classroom::where('name', $request->class_name)->first()->id;
        $fcourse_id = Course::where('code', $request->course_code)->first()->id;
        $fstaff_id = Staff::where('code', $request->staff_code)->first()->id;
        $semester = '';
        if (is_numeric($request->semester)){
            if ($request->semester % 2 == 0){
                $semester = 'even';
            } else {
                $semester = 'odd';
            }
        } else {
            $semester = $request->semester;
        }
        $facademic_year_id = AcademicYear::where('year', $request->academic_year)
                            ->where('semester', $semester)->first()->id;
        if (ClassCourse::where('class_id', $fclass_id)
            ->where('course_id', $fcourse_id)
            ->where('staff_id', $fstaff_id)
            ->where('academic_year_id', $facademic_year_id)
            ->first() == null) {
            $classcourse = ClassCourse::create([
                'class_id' => $fclass_id,
                'course_id' => $fcourse_id,
                'staff_id' => $fstaff_id,
                'academic_year_id' => $facademic_year_id
            ]);

            //Generate Module 1-14
            for ($i = 1; $i < 15; $i++) {
                $module = '';
                if (Module::where('course_id', $fcourse_id)->where('index', $i)->first() == null) {
                    $module = Module::create([
                        'course_id' => $fcourse_id,
                        'index' => $i,
                        'academic_year_id' => $facademic_year_id,
                    ]);
                    $module->save();
                } else {
                    $module = Module::where('course_id', $fcourse_id)->where('index', $i)->first();
                }

                //Generate Schedule 1-14
                $schedule = Schedule::create([
                    'name' => 'Module '.$i,
                    'time_start' => '2021-01-12 07:30:00',
                    'time_end' => '2021-01-12 10:30:00',
                    'room_id' => Room::first()->id,
                    'class_course_id' => ClassCourse::where('class_id', $fclass_id)
                                        ->where('course_id', $fcourse_id)
                                        ->where('staff_id', $fstaff_id)
                                        ->where('academic_year_id', $facademic_year_id)
                                        ->first()->id,
                    'module_id' => $module->id,
                    'academic_year_id' => $facademic_year_id,
                    'date' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
                $schedule->save();
            }
        }

        // create Student classes
        $student_id = DB::table('students')
                    ->where('nim', $request->nim)
                    ->first()->id;
        $class_course_id = ClassCourse::where('class_id', $fclass_id)
                    ->where('course_id', $fcourse_id)
                    ->where('staff_id', $fstaff_id)
                    ->where('academic_year_id', $facademic_year_id)
                    ->first()->id;
        if(StudentClassCourse::where('student_id', $student_id)->where('class_course_id', $class_course_id)->first() == null) {
            $student_class = StudentClassCourse::create([
                'student_id' => $student_id,
                'class_course_id' => $class_course_id,
            ]);
            return $this->response->item($student_class, new StudentClassCourseTransformer);
        }
    }

    public function delete(Request $request, $id)
    {
        $student_class_course = StudentClassCourse::findOrFail($id);
        // $student_class_course = StudentClassCourse::where('id', $id);
        $student_class_course->delete();
        return $this->response->noContent();
    }

    public function import(Request $request)
    {
        Excel::import(new StudentImport, request()->file('file'));
        return "import success";
    }

    public function student_classes(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required',
            'class_course_id' => 'required'
        ]);
        if (StudentClassCourse::where('student_id', $request->student_id)->where('class_course_id', $request->class_course_id)->first() == null) {
            $student_class = StudentClassCourse::create([
                'student_id' => $request->student_id,
                'class_course_id' => $request->class_course_id,
            ]);
            return $this->response->item($student_class, new StudentClassCourseTransformer);
        } else {
            return $this->response->errorNotFound('Duplicate data.');
        }
    }

    public function edit_student_classes(Request $request, $id)
    {
        $studentclass = StudentClassCourse::find($id);
        // print($id);
        $this->validate($request, [
            'student_id' => 'required',
            'class_course_id' => 'required'
        ]);
        // print('data'.$request->student_id);
        $studentclass->fill($request->all());
        $studentclass->save();
        return $this->response->item($studentclass, new StudentClassCourseTransformer);
    }

    public function delete_student_classes($id)
    {
        $studentclass = StudentClassCourse::find($id);
        $studentclass->delete();
        return $this->response->noContent();
    }

    public function get_student_classes(Request $request){
        $student_class = StudentClassCourse::query();

        if($request->has('student_id')) {
            $student_class = $student_class->where('student_id', $request->student_id);
        }

        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $student_class = $student_class->paginate($per_page);

        return $this->response->paginator($student_class, new StudentClassCourseTransformer);
    }

    public function set_role(Request $request)
    {
        $this->validate($request, [
            'no_induk' => 'required',
            'student' => 'required',
            'asprak' => 'required',
            'aslab' => 'required',
            'staff' => 'required',
            'laboran' => 'required',
            'dosen' => 'required'
        ]);

        if ($request->student == 1) {
            $student = Student::where('nim', $request->no_induk)->first();
            $user = User::find($student->user_id);

            if ($request->asprak == 0) {
                $user->removeRole(Role::ROLE_ASPRAK);
            } else {
                $user->assignRole(Role::ROLE_ASPRAK);
            }

            if ($request->aslab == 0) {
                $user->removeRole(Role::ROLE_ASLAB);
            } else {
                $user->assignRole(Role::ROLE_ASLAB);
            }
        } else {
            $staff = Staff::where('nip', $request->no_induk)->first();
            $user = User::find($staff->user_id);

            if ($request->laboran == 0) {
                $user->removeRole(Role::ROLE_LABORAN);
            } else {
                $user->assignRole(Role::ROLE_LABORAN);
            }

            if ($request->dosen == 0) {
                $user->removeRole(Role::ROLE_DOSEN);
            } else {
                $user->assignRole(Role::ROLE_DOSEN);
            }
        }
    }

    public function get_role($no_induk)
    {
        if (Student::where('nim', $no_induk)->first() != null) {
            $student = Student::where('nim', $no_induk)->first();
            $user = User::find($student->user_id);
            return $this->response->item($user, new UserTransformer);
        } elseif (Staff::where('nip', $no_induk)->first() != null) {
            $staff = Staff::where('nip', $no_induk)->first();
            $user = User::find($staff->user_id);
            return $this->response->item($user, new UserTransformer);
        } else {
            return $this->response->errorNotFound('NIM/NIP not found.');
        }
    }

    public function import_asprak(Request $request)
    {
        Excel::import(new AsprakImport, request()->file('file'));
        return "import success";
    }

    public function deleteall($table)
    {
        if ($table == 'users') {
            User::truncate();
            $laboran = Staff::create([
                'nip' => 'laboran',
                'name' => 'Laboran (Super admin)',
                'code' => 'laboran'
            ]);
            $laboran->save();
            $user = $laboran->user;
            $user->assignRole(Role::ROLE_LABORAN);
            return 'Delete '.$table;
        } elseif ($table == 'students' or $table == 'staffs') {
            if ($table == 'students') {
                $allStudent = Student::pluck('user_id');
            } else {
                $allStudent = Staff::pluck('user_id');
            }
            foreach ($allStudent as $key => $value) {
                $user = User::findOrFail($value);
                $user->delete();
            }

            if ($table == 'staffs') {
                $laboran = Staff::create([
                    'nip' => 'laboran',
                    'name' => 'Laboran (Super admin)',
                    'code' => 'laboran'
                ]);
                $laboran->save();
                $user = $laboran->user;
                $user->assignRole(Role::ROLE_LABORAN);
            }
            return 'Delete '.$table;
        } else {
            DB::table($table)->delete();
            return 'Delete '.$table;
        }
    }

    public function report_roles($role)
    {
        $users = User::get();
        $data = [];
        $i = 0;
        foreach ($users as $key => $user) {
            if ($role == 'asprak') {
                if($user->isAsprak()) {
                    $data['asprak'][$i++] = $user->student;
                }
            } elseif ($role == 'aslab') {
                if($user->isAslab()) {
                    $data['aslab'][$i++] = $user->student;
                }
            } elseif ($role == 'dosen') {
                if($user->isDosen()) {
                    $data['dosen'][$i++] = $user->staff;
                }
            } elseif ($role == 'staff') {
                if($user->isStaff()) {
                    $data['staff'][$i++] = $user->staff;
                }
            } elseif ($role == 'student') {
                if($user->isStudent()) {
                    $data['student'][$i++] = $user->student;
                }
            } elseif ($role == 'laboran') {
                if($user->isLaboran()) {
                    $data['laboran'][$i++] = $user->staff;
                }
            }
        }
        return $data;
    }

    public function info_bap($schedule_id)
    {
        $schedule = Schedule::where('id', $schedule_id)->first();
        if($schedule == null){
            return $this->response->noContent();
        }
        else{
            $id_classcourse = $schedule->class_course->id;

            $aspraks = Asprak::get();
            $students = StudentClassCourse::get();

            $data = [];
            $schedule->room;
            $schedule->class_course;
            $schedule->class_course->courses;
            $schedule->class_course->classes;
            $schedule->class_course->staffs;
            $schedule->module;
            $schedule->academic_year;
            $data['schedule'] = $schedule;

            $i = 0;
            foreach ($aspraks as $key => $asprak) {
                if ($asprak->class_course_id == $id_classcourse) {
                    $data_mhs = Student::where('id', $asprak->student_id)->first();
                    $data['asprak'][$i++] = $data_mhs;
                }
            }

            $i = 0;
            foreach ($students as $key => $student) {
                if ($student->class_course_id == $id_classcourse) {
                    $data_mhs = Student::where('id', $student->student_id)->first();
                    $data['student'][$i++] = $data_mhs;
                }
            }
            return $data;
        }
    }

    public function set_bap(Request $request, $schedule_id)
    {
        if (Schedule::findOrFail($schedule_id)) {
            $data = [];

            foreach ($request->asprak as $key => $asprak) {
                if (!AsprakPresence::where('student_id', $asprak)->where('schedule_id', $schedule_id)->first()) {
                    $asprak = AsprakPresence::create([
                        'student_id' => $asprak,
                        'schedule_id' => $schedule_id
                    ]);
                    $data['asprak'] = $asprak;
                }
            }

            foreach ($request->student as $key => $student) {
                if (!StudentPresence::where('student_id', $student)->where('schedule_id', $schedule_id)->first()) {
                    $student = StudentPresence::create([
                        'student_id' => $student,
                        'schedule_id' => $schedule_id
                    ]);
                    $data['student'] = $student;
                }
            }

            if (!Bap::where('schedule_id', $schedule_id)->first()) {
                $bap = Bap::create([
                    'schedule_id' => $schedule_id,
                    'materi' => $request->materi,
                    'evaluasi' => $request->evaluasi,
                    'jenis' => $request->jenis,
                ]);
                $data['bap'] = $bap;
            }

            return $data;
        }
    }

    public function show_bap(Request $request) {
        $class_course = ClassCourse::query();

        if($request->has('asprak_id')) {
            if(!empty($request->asprak_id)) {
                $student_class_course = Asprak::where('student_id', $request->asprak_id)->get();
                $x = [];
                foreach($student_class_course as $key=>$scc) {
                    $x[$key] = $scc->class_course_id;
                }
                $class_course = $class_course->whereIn('id', $x);
            }
        }
        if($request->has('class_name')) {
            if(!empty($request->class_name)) {
                $classroom = Classroom::where('name', 'like','%'.$request->class_name.'%')
                                ->first();
                $class_course = $class_course->where('class_id', $classroom->id);
            }
        }
        if($request->has('course_name')) {
            if(!empty($request->course_name)) {
                $course = Course::where('name', 'like','%'.$request->course_name.'%')
                            ->first();
                $class_course = $class_course->where('course_id', $course->id);
            }
        }
        if($request->has('academic_year_id')) {
            if(!empty($request->academic_year_id)) {
                $class_course = $class_course->where('academic_year_id', $request->academic_year_id);
            }
        }
        $class_course = $class_course->get();

        $data['total_data'] = 0;
        $data['data'] = [];
        foreach($class_course as $key=>$cc){
            $class_course_id = $cc['id'];
            $schedules = Schedule::Where('class_course_id', $class_course_id)->get();

            $arr = [];
            foreach($schedules as $key=>$s) {
                $course = Course::where('id', $cc['course_id'])->first();
                $module = Module::where('id', $s['module_id'])->first();
                $academic_year = AcademicYear::where('id', $s['academic_year_id'])->first();
                $class = Classroom::where('id', $cc['class_id'])->first();
                $staff = Staff::where('id', $cc['staff_id'])->first();


                $arr[$key]['id'] = $s['id'];
                $arr[$key]['title'] = $s['name'];
                $arr[$key]['start'] = $s['time_start'];
                $arr[$key]['end'] = $s['time_end'];
                $arr[$key]['class_course']['course']['name'] = $course['name'];
                $arr[$key]['module'] = $module;
                $arr[$key]['class_course']['class']['name'] = $class['name'];
                $arr[$key]['class_course']['staff']['code'] = $staff['code'];
                $arr[$key]['date'] = $s['date'];
                $arr[$key]['academic_year'] = $academic_year;
                if (Bap::where('schedule_id', $s['id'])->first()) {
                    $arr[$key]['is_present'] = true;
                }
                else{
                    $arr[$key]['is_present'] = false;
                }

            }
            foreach($arr as $key=>$a){
                array_push($data['data'],$a);
            }
            $data['total_data'] = $data['total_data'] + count($arr);
        }
        return $data;
    }

    public function show_bap_detail($schedule_id)
    {
        if (empty(Schedule::find($schedule_id))) {
            return $this->response->noContent();
        } else{
            $bap = Bap::where('schedule_id', $schedule_id)->get();
            if($bap == null){
                $data = [];
                $data['data'] = "";
                return $data;
            }
            else{
                $data = [];
                $data['bap'] = $bap;

                $schedule = Schedule::where('id', $schedule_id)->first();
                $schedule->room;
                $schedule->class_course;
                $schedule->class_course->courses;
                $schedule->class_course->classes;
                $schedule->class_course->staffs;
                $schedule->module;
                $schedule->academic_year;
                $data['schedule'] = $schedule;

                $asprak_presence = AsprakPresence::where('schedule_id', $schedule_id)->get();
                $student_presence = StudentPresence::where('schedule_id', $schedule_id)->get();
                // $data['asprak_presence'] = $asprak_presence;
                // $data['student_presence'] = $student_presence;

                $i = 0;
                foreach ($asprak_presence as $key => $asprak) {
                    $data_mhs = Student::where('id', $asprak->student_id)->first();
                    $data['asprak_presence'][$i++] = $data_mhs;
                }

                $i = 0;
                foreach ($student_presence as $key => $student) {
                    $data_mhs = Student::where('id', $student->student_id)->first();
                    $data['student_presence'][$i++] = $data_mhs;
                }

                return $data;
            }
        }
    }

    public function manage_account_username(Request $request, $id) 
    {
        $user_id = null;
        $username = null;
        if (empty(Student::find($id))) {
            if (empty(Staff::find($id))) {
                return $this->response->errorNotFound('invalid user id');
            } else {
                $this->validate($request, [
                    'nip' => [
                        'required',
                        Rule::unique('staffs')
                    ],
                ]);
                $staff = Staff::find($id);

                $staff->nip = $request->nip;
                $staff->save();
                $username = $staff->nip;
                $user_id = $staff->user_id;
            }
        } else {
            $this->validate($request, [
                'nim' => [
                    'required',
                    Rule::unique('students')
                ],
            ]);
            $student = Student::find($id);
            $student->nim = $request->nim;
            $student->save();
            $username = $student->nim;
            $user_id = $student->user_id;
        }
        $user = User::find($user_id);        
        $user->username = $username;
        $user->save();
    }

    public function manage_account_password(Request $request, $id) 
    {
        $this->validate($request, [
            'new_password' => 'required|min:5',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        $user_id = null;
        if (empty(Student::find($id))) {
            if (empty(Staff::find($id))) {
                return $this->response->errorNotFound('invalid user id');
            } else {
                $staff = Staff::find($id);
                $user_id = $staff->user_id;
            }
        } else {
            $student = Student::find($id);
            $user_id = $student->user_id;
        }
        $user = User::find($user_id);
        
        $user->password = Hash::make($request->input('new_password'));
        $user->save();
    }

    public function reset_password($id){
        if (empty(Student::find($id))) {
            if (empty(Staff::find($id))) {
                return $this->response->errorNotFound('invalid user id');
            } else {
                $staff = Staff::find($id);
                $user = User::find($staff->user_id);
                $user->password = Hash::make($staff->nip);
                $user->save();
                return $this->response->noContent();
            }
        } else {
            $student = Student::find($id);
            $user = User::find($student->user_id);
            $user->password = Hash::make($student->nim);
            $user->save();
            return $this->response->noContent();
        }
    }

    // function logout belum selesai
        
    public function logout_user($id){
        if (empty(Student::find($id))) {
            if (empty(Staff::find($id))) {
                return $this->response->errorNotFound('invalid user id');
            } else {
                $staff = Staff::find($id);
                $user = User::find($staff->user_id);
                Auth::setUser($user);
                Auth::logout();
                // return redirect('/');
                return $this->response->noContent();
            }
        } else {
            $student = Student::find($id);
            $user = User::find($student->user_id);
            Auth::setUser($user);
            Auth::logout();
        }
    }

    public function index_user(Request $request)
    {
        $user = User::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$user) {
            $user = $user->where('username', 'ILIKE', '%'.$search.'%');
                            // ->orWhere('name', 'ILIKE', '%'.$search.'%')
                            // ->orWhere('nim', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $user->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $user->orderBy($orderBy);
        }

        $user = $user->paginate($per_page);

        return $this->response->paginator($user, new UserTransformer);
    }
}
