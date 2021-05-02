<?php

namespace App\Http\Controllers\Api\V1\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\User;
use App\Transformers\LaboranTransformer;
use App\Transformers\StudentTransformer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Imports\CourseImport;
use App\Imports\ClassroomImport;
use App\Imports\StudentImport;
use App\Imports\StudentClassImport;
use Maatwebsite\Excel\Facades\Excel;

class LaboranController extends BaseController
{
    public function index(Request $request)
    {
        $users = DB::table('students')
            ->join('students_classes', 'students.id', '=', 'students_classes.student_id')
            ->join('classes', 'classes.id', '=', 'students_classes.class_id')
            ->join('courses', 'courses.id', '=', 'classes.course_id')
            ->join('staffs', 'staffs.id', '=', 'classes.staff_id')
            ->select('students.id', 'students.nim', 'students.name', 'classes.name as class_name', 'students.gender', 
            'students.religion', 'courses.code as course_code', 'courses.name as course_name',
            'staffs.code as staff_code', 'classes.academic_year', 'classes.semester');
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

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
            ->select('students.id', 'students.nim', 'students.name', 'classes.name as class_name', 'students.gender', 
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
                Rule::unique('students')
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
        $student = Student::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'gender' => $request->gender,
            'religion' => $request->religion
        ]);

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
        return $this->response->item($student, new StudentTransformer);
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
}
