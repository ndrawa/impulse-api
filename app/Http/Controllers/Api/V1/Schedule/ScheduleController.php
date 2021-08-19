<?php

namespace App\Http\Controllers\Api\V1\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;
use App\Models\StudentClassCourse;
use App\Models\ClassCourse;
use App\Models\AcademicYear;
use App\Models\Staff;
use App\Models\Room;
use App\Models\Module;
use App\Transformers\ScheduleTransformer;
use App\Transformers\TestTransformer;
use App\Transformers\ClassCourseTransformer;
use Illuminate\Validation\Rule;
use App\Imports\ScheduleImport;
use Maatwebsite\Excel\Facades\Excel;


class ScheduleController extends BaseController
{
    public function index(Request $request)
    {
        $schedules = Schedule::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$staffs) {
            $schedules = $schedules->where('name', 'LIKE', '%'.$search.'%');
        });

        $schedules = $schedules->paginate($per_page);

        return $this->response->paginator($schedules, new ScheduleTransformer);
    }

    public function index_simple(Request $request) {
        $schedules = Schedule::get();
        $arr = [];
        foreach($schedules as $key=>$s) {
            $class_course = ClassCourse::where('id', $s['class_course_id'])->first();
            $class = Classroom::where('id', $class_course['class_id'])->first();
            $course = Course::where('id', $class_course['course_id'])->first();
            $staff = Staff::where('id', $class_course['staff_id'])->first();
            $class_academic_year = Staff::where('id', $class_course['staff_id'])->first();
            $academic_year = AcademicYear::where('id', $s['academic_year_id'])->first();
            $module = Module::where('id', $s['module_id'])->first();
            $room = Room::where('id', $s['room_id'])->first();

            $arr[$key]['id'] = $s['id'];
            $arr[$key]['title'] = $s['name'];
            $arr[$key]['start'] = $s['time_start'];
            $arr[$key]['end'] = $s['time_end'];
            $arr[$key]['room'] = $room;
            $arr[$key]['class_course']['id'] = $class_course['id'];
            $arr[$key]['class_course']['class']['id'] = $class['id'];
            $arr[$key]['class_course']['class']['name'] = $class['name'];
            $arr[$key]['class_course']['class']['academic_year'] = $class['academic_year'];
            $arr[$key]['class_course']['course']['id'] = $course['id'];
            $arr[$key]['class_course']['course']['name'] = $course['name'];
            $arr[$key]['class_course']['staff']['id'] = $staff['id'];
            $arr[$key]['class_course']['staff']['name'] = $staff['name'];
            $arr[$key]['class_course']['staff']['code'] = $staff['code'];
            $arr[$key]['module'] = $module;
            $arr[$key]['academic_year'] = $academic_year;
            $arr[$key]['date'] = $s['date'];
        }

        $data['data'] = $arr;
        return $data;
    }

    public function import(Request $request) {
        Excel::import(new ScheduleImport, request()->file('file'));

        return 'import success';
    }

    public function create(Request $request)
    {
        // $this->authorize('create', $this->user());
        $this->validate($request, [
            'name' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'room_id' => 'required',
            'class_course_id' => 'required',
            'academic_year_id' => 'required',
            'module_id' => 'required',
            'date' => 'required',
        ]);
        $schedule = Schedule::create($request->all());

        return $this->response->item($schedule, new ScheduleTransformer);
    }

    public function getTest(Request $request, $id) {
        // $id = test_id
        $test = Test::firstWhere('id', $id);

        return $this->response->item($test, new TestTransformer);
    }

    public function create_test(Request $request) {
        $this->validate($request, [
            'type' => 'required',
            'questions' => 'required',
        ]);

        $test = Test::create([
            'type' => $request->type,
        ]);

        $questions = $this->add_question($request, $test->id);

        return json_encode(['msg' => 'success']);
    }

    public function delete_test(Request $request, $id) {
        $test = Test::findOrFail($id);
        $test->delete();

        return $this->response->noContent();
    }

    public function add_question(Request $request, $test_id) {
        if($request->type == 'essay') {
            foreach($request->questions as $key=>$q) {
                $qstion = Question::create([
                    'test_id' => $test_id,
                    'question' => $q['text'],
                ]);
                $answer = Answer::create([
                    'question_id' => $qstion['id'],
                    'answer' => $q['answer'],
                ]);
            }
        } elseif ($request->type == 'multiple_choice') {
            foreach($request->questions as $key=>$q) {
                $qstion = Question::create([
                    'test_id' => $test_id,
                    'question' => $q['text'],
                ]);

                foreach($request->questions[$key]['options'] as $key=>$ans) {
                    $answer = Answer::create([
                        'question_id' => $qstion['id'],
                        'answer' => $ans['text'],
                        'is_answer' => $ans['is_answer'],
                    ]);
                }
            }
        } elseif ($request->type == 'file') {
            //TBA
        } elseif ($request->type == 'pdf') {
            //TBA
        } else {
            return $this->response->noContent();
        }
    }

    public function get_student_class_course(Request $request, $student_id) {
        $class_course_in_scc = StudentClassCourse::where('student_id',$student_id)->get();
        if($class_course_in_scc != NULL) {
            $data = [];
            foreach($class_course_in_scc as $key=>$cc_scc) {
                $class_course = ClassCourse::find($cc_scc->class_course_id);
                $course = $class_course->courses;
                $class = $class_course->classes;
                $academic_year = $class_course->academic_years;
                $academic_year = AcademicYear::firstWhere('id', $class_course->academic_year_id);

                $data['data'][$key]['id'] = $class_course->id;
                $data['data'][$key]['name'] = $course->name.'/'.$class->name.' ('.$academic_year->year.')';
            }
            return $data;

            $class_course = ClassCourse::find('id', $students_class_course->class_course_id);
            $data = [];
            $data['id'] = $class_course->id;
            foreach($class_course as $key=>$cc) {

            }
            return $data;
        }

        return $this->response->error('student id not found', 404);
    }

    public function show_schedule(Request $request, $class_course_id){
        $schedule = Schedule::Where('class_course_id', $class_course_id)->get();

        return $this->response->item($schedule, new ScheduleTransformer);
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        // $this->authorize('update', $schedule);
        $this->validate($request, [
            'name' => 'required',
            'time_start' => [
                'required'
            ],
            'time_end' => [
                'required'
            ],
            'room_id' => [
                'required'
            ],
            'class_course_id' => [
                'required'
            ],
            'academic_year_id' => [
                'required'
            ],
            'module_id' => [
                'required'
            ],
            'date' => [
                'required'
            ]
        ]);
        $schedule->fill($request->all());
        $schedule->save();

        return $this->response->item($schedule, new ScheduleTransformer);
    }
}
