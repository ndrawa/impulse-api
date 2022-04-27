<?php

namespace App\Http\Controllers\Api\V1\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Student;
use App\Models\User;
use App\Models\Grade;
use App\Models\ClassCourse;
use App\Models\Ticket;
use App\Transformers\StudentTransformer;
use App\Transformers\StudentMePresenceTransformer;
use App\Transformers\TicketTransformer;
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

    public function show_me_presence($class_course_id = null) {
        $student = Student::with(['student_class_course',
                    'student_class_course.class_course',
                    'student_class_course.class_course.courses',
                    'student_class_course.class_course.classes',
                    'student_class_course.class_course.schedule',
                    'student_class_course.class_course.schedule.student_presence'])
                    ->find($this->user->student->id);

        $grade_controller = new GradeController;
        $grade = json_decode($grade_controller->getStudentGrades($this->user->student->id), true);


        return $this->simplify_show_me_presence($student, $grade['data']['result'], $class_course_id);

        return array('data' =>
                        $this->simplify_show_me_presence($student, $grade['data']['result'], $class_course_id));
    }

    private function simplify_show_me_presence($student, $grade, $class_course_id = null) {
        $data = array(
            'student' => array(
                'id' => $student->id,
                'nim' => $student->nim,
                'nama' => $student->name,
            ),
        );

        if($class_course_id != null){
            $class_course = ClassCourse::findOrFail($class_course_id);
            foreach($student->student_class_course as $key=>$d){
                if($d->class_course->id == $class_course_id){
                    $class_course = $d;
                    break;
                }
            }

            if($class_course != null){
                $data['class_course'] = array(
                    'id' => $class_course->class_course->id,
                    'course' => array(
                        'id' => $class_course->class_course->courses->id,
                        'code' => $class_course->class_course->courses->code,
                        'name' => $class_course->class_course->courses->name,
                    ),
                    'class' => array(
                        'id' => $class_course->class_course->classes->id,
                        'name' => $class_course->class_course->classes->name,
                    ),
                );

                $schd = $this->sort_module($class_course->class_course->schedule);

                foreach($schd as $sched_key=>$sched) {
                    $presence = false;

                    if(count($sched->student_presence)) {
                        $presence = $this->checkMePresence($this->user->student->id, $sched->student_presence);
                    }

                    $grade = json_decode(app('App\Http\Controllers\Api\V1\GradeController')
                                        ->getStudentGrades($this->user->student->id, $class_course->class_course->courses->id));

                    $data['class_course']['modules'][$sched_key] = array(
                        'presence' => $presence,
                        'grade' => $grade->data->result[0]->modules[$sched_key],
                    );
                }
            }
        }
        else{
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

                $schd = $this->sort_module($d->class_course->schedule);

                foreach($schd as $sched_key=>$sched) {
                    $presence = false;
                    if(count($sched->student_presence)) {
                        $presence = $this->checkMePresence($this->user->student->id, $sched->student_presence);
                    }

                    $grade = json_decode(app('App\Http\Controllers\Api\V1\GradeController')
                            ->getStudentGrades($this->user->student->id, $d->class_course->courses->id));

                    $data['class_course'][$key]['modules'][$sched_key] = array(
                        'presence' => $presence,
                        'grade' => $grade->data->result[0]->modules[$sched_key],
                    );
                }
            }
        }

        return $data;
    }

    public function sort_module($schedule) {
        $size = count($schedule);
        $temp;
        for($i=0; $i<$size; $i++) {
            for($j=0; $j<$size-$i-1; $j++) {
                if($schedule[$j]['module']['index'] > $schedule[$j+1]['module']['index']) {
                    $temp = $schedule[$j];
                    $schedule[$j] = $schedule[$j+1];
                    $schedule[$j+1] = $temp;
                }
            }
        }

        return $schedule;
    }

    public function checkMePresence($student_id, $obj_student_presence) {
        $arr_student_presence = array($obj_student_presence);
        foreach($arr_student_presence[0] as $student_presence) {
            if(!strcmp($student_presence['student_id'], $student_id)) {
                return true;
            }
        }

        return false;
    }

    public function index_ticket(Request $request)
    {
        $tickets = Ticket::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$tickets) {
            $tickets = $tickets->where('name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('nim', 'ILIKE', '%'.$search.'%')
                            ->orWhere('course_name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('class_name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('practicum_day', 'ILIKE', '%'.$search.'%')
                            ->orWhere('practice_session', 'ILIKE', '%'.$search.'%')
                            ->orWhere('username_sso', 'ILIKE', '%'.$search.'%')
                            ->orWhere('password_sso', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $tickets->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $tickets->orderBy($orderBy);
        }

        $tickets = $tickets->paginate($per_page);

        return $this->response->paginator($tickets, new TicketTransformer);
    }

    public function show_ticket(Request $request, $nim)
    {
        $ticket = Ticket::where('nim', $nim)->first();
        return $this->response->item($ticket, new TicketTransformer);
    }

    public function create_ticket(Request $request)
    {
        $this->validate($request, [
            'nim' => 'required',
            'name' => 'required',
            'course_name' => 'required',
            'class_name' => 'required',
            'practicum_day' => 'required',
            'practice_session' => 'required',
            'username_sso' => 'required',
            'password_sso' => 'required',
            'note_student' => 'required',
        ]);
        $ticket = Ticket::create($request->all());

        return $this->response->item($ticket, new TicketTransformer);
    }

    public function update_ticket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->validate($request, [
            'note_confirmation' => 'required'
        ]);
        $ticket->fill($request->all());
        $ticket->save();

        return $this->response->item($ticket, new TicketTransformer);
    }

    public function delete_ticket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return $this->response->noContent();
    }
}
