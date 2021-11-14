<?php

namespace App\Http\Controllers\Api\V1\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Student;
use App\Models\User;
use App\Models\Grade;
use App\Transformers\StudentTransformer;
use App\Transformers\StudentMePresenceTransformer;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\V1\GradeController as GradeController;


class StudentController extends BaseController
{
    public function index(Request $request)
    {
        $students = Student::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$students) {
            $students = $students->where('name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('nim', 'ILIKE', '%'.$search.'%')
                            ->orWhere('gender', 'ILIKE', '%'.$search.'%')
                            ->orWhere('religion', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $students->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $students->orderBy($orderBy);
        }

        $students = $students->paginate($per_page);

        return $this->response->paginator($students, new StudentTransformer);
    }

    public function show(Request $request, $id)
    {
        //$student = Student::findOrFail($id);
        $student = Student::where('nim', $id)->first();
        return $this->response->item($student, new StudentTransformer);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Student::class);
        $this->validate($request, [
            'name' => 'required',
            'nim' => [
                'required',
                Rule::unique('students')
            ],
            'gender' => [
                'required'
            ],
            'religion' => [
                'required'
            ]
        ]);
        $student = Student::create($request->all());

        return $this->response->item($student, new StudentTransformer);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('update', $student);
        $this->validate($request, [
            'name' => 'required',
            'nim' => [
                'required',
                Rule::unique('students')->ignore($student->nim, 'nim')
            ],
            'gender' => [
                'required'
            ],
            'religion' => [
                'required'
            ]
        ]);
        $student->fill($request->all());
        $student->save();

        return $this->response->item($student, new StudentTransformer);
    }

    public function delete(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $user = User::findOrFail($student->user_id);
        $this->authorize('delete', $student);
        $user->delete();

        return $this->response->noContent();
    }

    public function show_student_grade(Request $request, $user_id){
        $grade = Grade::Where('user_id', $user_id)->get();

        return $grade;
    }

    public function show_me_presence() {
        $student = Student::with(['student_class_course',
                    'student_class_course.class_course',
                    'student_class_course.class_course.courses',
                    'student_class_course.class_course.classes',
                    'student_class_course.class_course.schedule',
                    'student_class_course.class_course.schedule.student_presence'])
                    ->find($this->user->student->id);

        $grade_controller = new GradeController;
        $grade = json_decode($grade_controller->getStudentGrades($this->user->student->id), true);

        return $data = array( 'data'=>$this->simplify_show_me_presence($student, $grade['data']['result']));
    }

    private function simplify_show_me_presence($student, $grade) {
        $data = array(
            'student' => array(
                'id' => $student->id,
                'nim' => $student->nim,
                'nama' => $student->name,
            ),
        );
        foreach($student->student_class_course as $key=>$d) {
            $data['class_course'][$key] = array(
                'id' => $d->class_course->id,
                'course' => array(
                    'id' => $d->class_course->courses->id,
                    'code' => $d->class_course->courses->code,
                    'name' => $d->class_course->courses->name,
                ),
                'class' => array(
                    'id' => $d->class_course->classes->id,
                    'name' => $d->class_course->classes->name,
                ),
            );

            foreach($d->class_course->schedule as $cc_key=>$cc) {
                $i = $cc_key;
                $presence = false;
                if(count($cc->student_presence)) {
                    $presence = true;
                }
                $data['class_course'][$key]['presences'][$cc_key] = array(
                    'index' => $i+1,
                    'presence' => $presence,
                    'grade' => array(
                        'pretest_grade' => $grade[$key]['modules'][$cc_key]['pretest_grade'],
                        'journal_grade' => $grade[$key]['modules'][$cc_key]['journal_grade'],
                        'posttest_grade' => $grade[$key]['modules'][$cc_key]['posttest_grade'],
                    ),
                );
            }
        }

        return $data;
    }
}
