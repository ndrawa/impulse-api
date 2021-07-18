<?php

namespace App\Http\Controllers\Api\V1\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Classroom;
use App\Models\ClassCourse;
use App\Models\Course;
use App\Models\User;
use App\Models\AcademicYear;
use App\Transformers\LaboranTransformer;
use App\Transformers\StudentTransformer;
use App\Transformers\ClassTransformer;
use App\Transformers\ClassCourseTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Imports\StudentImport;
use App\Imports\StudentClassImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Role;

class LaboranController extends BaseController
{
    public function index(Request $request)
    {
        $users = DB::table('students')
            ->join('students_classes', 'students.id', '=', 'students_classes.student_id')
            ->join('classes', 'classes.id', '=', 'students_classes.class_id')
            ->join('courses', 'courses.id', '=', 'classes.course_id')
            ->join('staffs', 'staffs.id', '=', 'classes.staff_id')
            ->select('students.id as student_id', 'students.nim', 'students.name', 'classes.id as class_id', 'classes.name as class_name', 'students.gender',
            'students.religion', 'courses.code as course_code', 'courses.name as course_name',
            'staffs.code as staff_code', 'classes.academic_year', 'classes.semester');
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        if ($request->has('kelas') && $request->has('course')) {
            $kelas = $request->get('kelas');
            $course = $request->get('course');

            if ($request->has('search')) {
                $request->whenHas('search', function($search) use (&$users, &$kelas, &$course) {
                    $users = $users->where('classes.name', 'ILIKE', '%'.$kelas.'%')
                                    ->where('courses.name', 'ILIKE', '%'.$course.'%')
                                    ->where(function ($query) use (&$search) {
                                        $query->where('students.name', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('students.nim', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('courses.code', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('staffs.code', 'ILIKE', '%'.$search.'%',)
                                                ->orWhere('classes.academic_year', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('classes.semester', 'ILIKE', '%'.$search.'%');
                                        });
                                    // ->orWhere('students.gender', 'ILIKE', '%'.$search.'%')
                                    // ->orWhere('students.religion', 'ILIKE', '%'.$search.'%')
                });
            }
            else {
                $users = $users->where('classes.name', 'ILIKE', '%'.$kelas.'%')
                                ->where('courses.name', 'ILIKE', '%'.$course.'%');
            }
        }
        elseif ($request->has('kelas')) {
            $kelas = $request->get('kelas');

            if ($request->has('search')) {
                $request->whenHas('search', function($search) use (&$users, &$kelas) {
                    $users = $users->where('classes.name', 'ILIKE', '%'.$kelas.'%')
                                    ->where(function ($query) use (&$search) {
                                        $query->where('students.name', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('courses.name', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('students.nim', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('courses.code', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('staffs.code', 'ILIKE', '%'.$search.'%',)
                                                ->orWhere('classes.academic_year', 'ILIKE', '%'.$search.'%')
                                                ->orWhere('classes.semester', 'ILIKE', '%'.$search.'%');
                                        });
                                    // ->orWhere('students.gender', 'ILIKE', '%'.$search.'%')
                                    // ->orWhere('students.religion', 'ILIKE', '%'.$search.'%')
                });
            }
            else {
                $users = $users->where('classes.name', 'ILIKE', '%'.$kelas.'%');
            }
        }
        else {
            $request->whenHas('search', function($search) use (&$users) {
                $users = $users->where('students.name', 'ILIKE', '%'.$search.'%')
                                ->orWhere('students.nim', 'ILIKE', '%'.$search.'%')
                                ->orWhere('classes.name', 'ILIKE', '%'.$search.'%')
                                ->orWhere('students.gender', 'ILIKE', '%'.$search.'%')
                                ->orWhere('students.religion', 'ILIKE', '%'.$search.'%')
                                ->orWhere('courses.code', 'ILIKE', '%'.$search.'%')
                                ->orWhere('courses.name', 'ILIKE', '%'.$search.'%')
                                ->orWhere('staffs.code', 'ILIKE', '%'.$search.'%')
                                ->orWhere('classes.academic_year', 'ILIKE', '%'.$search.'%')
                                ->orWhere('classes.semester', 'ILIKE', '%'.$search.'%');
            });
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
            ->join('students_classes', 'students.id', '=', 'students_classes.student_id')
            ->join('classes', 'classes.id', '=', 'students_classes.class_id')
            ->join('courses', 'courses.id', '=', 'classes.course_id')
            ->join('staffs', 'staffs.id', '=', 'classes.staff_id')
            ->select('students.id as student_id', 'students.nim', 'students.name', 'classes.id as classes_id', 'classes.name as class_name', 'students.gender',
            'students.religion', 'courses.code as courses_code', 'courses.name as course_name',
            'staffs.code as staff_code', 'classes.academic_year', 'classes.semester')
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

        // create Course
        if (Course::where('code', $request->course_code)->first() == null) {
            $course = Course::create([
                'name' => $request->course_name,
                'code' => $request->course_code
            ]);
        }

        // create Class
        if (Classroom::where('name', $request->class_name)->first() == null) {
            $staff_id = DB::table('staffs')
                    ->where('code', $request->staff_code)
                    ->first();
            $course_id = DB::table('courses')
                    ->where('code', $request->course_code)
                    ->first();
            $classroom = Classroom::create([
                'staff_id' => $staff_id->id,
                'name' => $request->class_name,
                'course_id' => $course_id->id,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester
            ]);
        }

        // create Student classes
        $student_id = DB::table('students')
                    ->where('nim', $request->nim)
                    ->first();
        $class_id = DB::table('classes')
                    ->where('name', $request->class_name)
                    ->first();

        $student_class = StudentClass::create([
            'student_id' => $student_id->id,
            'class_id' => $class_id->id,
        ]);
        return $this->response->item($student_class, new StudentClassTransformer);
    }

    public function delete(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('delete', $student);
        $user = User::findOrFail($student->user_id);
        $user->delete();

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
            'class_id' => 'required'
        ]);
        if (StudentClass::where('student_id', $request->student_id)->where('class_id', $request->class_id)->first() == null) {
            $student_class = StudentClass::create([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
            ]);
            return $this->response->item($student_class, new StudentClassTransformer);
        } else {
            return $this->response->errorNotFound('Duplicate data.');
        }
    }

    public function edit_student_classes(Request $request, $id)
    {
        $studentclass = StudentClass::find($id);
        print($id);
        $this->validate($request, [
            'student_id' => 'required',
            'class_id' => 'required'
        ]);
        print('data'.$request->student_id);
        $studentclass->fill($request->all());
        $studentclass->save();
    }

    public function delete_student_classes($id)
    {
        $studentclass = StudentClass::find($id);
        $studentclass->delete();
        return $this->response->noContent();
    }

    public function get_student_classes(){
        $student_class = StudentClass::query()->get();
        return $this->response->item($student_class, new StudentClassTransformer);
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

    public function create_class_course(Request $request) {
        $this->validate($request, [
            'class_id' => 'required',
            'staff_id' => 'required',
            'course_id' => 'required',
            'academic_year_id' => 'required',
        ]);

        $classroom = Classroom::find($request->class_id);
        $staff = Staff::find($request->staff_id);
        $course = Course::find($request->course_id);
        $academic_year = AcademicYear::find($request->academic_year_id);

        if($classroom && $staff && $course && $academic_year) {
            $class_course = ClassCourse::create($request->all());
            $class_course->save();
        } else {
            return $this->response->errorNotFound('invalid id');
        }
    }

    public function get_class_course(Request $request) {
        $class_course = ClassCourse::all();

        $kelas = null;
        if($request->has('kelas')) {
            $kelas = strtoupper($request->get('kelas'));
        }
        $index = 0;
        $idx = 0;

        $arr = [];
        foreach($class_course as $key=>$cc) {
            $classroom = Classroom::select('name')->where('id', $cc['class_id'])->first();
            $staff = Staff::select('name')->where('id', $cc['staff_id'])->first();
            $course = Course::select('name')->where('id', $cc['course_id'])->first();
            $academic_year = AcademicYear::where('id', $cc['academic_year_id'])->first();

            $isTrue = false;
            if($kelas != null){
                $isTrue = str_contains($classroom->name, $kelas);
            }

            if ($kelas == null || $isTrue){
                if($isTrue){
                    $idx = $index;
                    $index++;
                }
                else{
                    $idx = $key;
                }
                $arr[$idx]['id'] = $cc['id'];
                $arr[$idx]['class']['id'] = $cc['class_id'];
                $arr[$idx]['class']['name'] = $classroom->name;
                $arr[$idx]['staff']['id'] = $cc['staff_id'];
                $arr[$idx]['staff']['name'] = $staff->name;
                $arr[$idx]['course']['id'] = $cc['course_id'];
                $arr[$idx]['course']['name'] = $course->name;
                $arr[$idx]['academic_year']['id'] = $cc['academic_year_id'];
                $arr[$idx]['academic_year']['name'] = $academic_year->year;
                $arr[$idx]['academic_year']['semester'] = $academic_year->semester;
            }
        }

        $data['data'] = $arr;

        return $data;
    }

    public function get_class_course_staff_year(Request $request) {
        $data['data']['classes'] = Classroom::select('id','name')->get();
        $data['data']['courses'] = Course::select('id','code','name')->get();
        $data['data']['staffs'] = Staff::select('id','code','name')->get();
        $data['data']['academic_year'] = AcademicYear::select('id','year','semester')->get();

        return $data;
    }

    public function delete_class_course_by_id(Request $request, $class_course_id) {
        // Hapus menggunakan id:
        // {{url}}/v1/laboran/class-course/{class_course_id}
        // Hapus semua:
        // {{url}}/v1/laboran/class-course/all
        if($class_course_id != 'all') {
            $class_course = ClassCourse::find($class_course_id);

            if(!$class_course) {
                return $this->response->errorNotFound('invalid id');
            }

            $class_course->delete();
        }
        if($class_course_id == 'all') {
            $class_course = ClassCourse::truncate();
        }

        return $this->response->noContent();
    }

}
