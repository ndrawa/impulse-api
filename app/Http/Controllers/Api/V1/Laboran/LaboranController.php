<?php

namespace App\Http\Controllers\Api\V1\Laboran;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Classroom;
use App\Models\Course;
use App\Transformers\LaboranTransformer;
use App\Transformers\StudentTransformer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LaboranController extends BaseController
{
    public function index(Request $request)
    {
        $users = DB::table('students')
            ->join('students_classes', 'students.id', '=', 'students_classes.student_id')
            ->join('classes', 'classes.id', '=', 'students_classes.class_id')
            ->join('courses', 'courses.id', '=', 'classes.course_id')
            ->join('staffs', 'staffs.id', '=', 'classes.staff_id')
            ->select('students.nim', 'students.name', 'classes.name as class_name', 'students.gender', 
            'students.religion', 'courses.code as courses_code', 'courses.name as course_name',
            'staffs.code as staff_code', 'classes.academic_year', 'classes.semester')
            ->get();
        // $per_page = env('PAGINATION_SIZE', 15);
        // $request->whenHas('per_page', function($size) use (&$per_page) {
        //     $per_page = $size;
        // });

        // $request->whenHas('search', function($search) use (&$users) {
        //     $users = $users->where('name', 'LIKE', '%'.$search.'%');
        // });

        // $users = $users->paginate($per_page);

        // return $this->response->paginator($users, new LaboranTransformer);
        return $this->response->item($users, new LaboranTransformer);
    }

    public function show(Request $request, $id)
    {
        $users = DB::table('students')
            ->join('students_classes', 'students.id', '=', 'students_classes.student_id')
            ->join('classes', 'classes.id', '=', 'students_classes.class_id')
            ->join('courses', 'courses.id', '=', 'classes.course_id')
            ->join('staffs', 'staffs.id', '=', 'classes.staff_id')
            ->select('students.nim', 'students.name', 'classes.name as class_name', 'students.gender', 
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
        if (Course::where('name', $request->course_name)->first() == null) {
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
        $student->delete();

        return $this->response->noContent();
    }
}
