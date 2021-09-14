<?php

namespace App\Http\Controllers\Api\V1\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Answer;
use App\Models\Asprak;
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
use Illuminate\Support\Facades\Storage;


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
        $class_course = ClassCourse::query();

        if($request->has('student_id')) {
            if(!empty($request->student_id)) {
                $student_class_course = StudentClassCourse::where('student_id', $request->student_id)->get();
                $x = [];
                foreach($student_class_course as $key=>$scc) {
                    $x[$key] = $scc->class_course_id;
                }
                $class_course = $class_course->whereIn('id', $x);
            }
        }

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
                $arr[$key]['class_course']['course']['code'] = $course['code'];
                $arr[$key]['class_course']['staff']['id'] = $staff['id'];
                $arr[$key]['class_course']['staff']['name'] = $staff['name'];
                $arr[$key]['class_course']['staff']['code'] = $staff['code'];
                $arr[$key]['module'] = $module;
                $arr[$key]['academic_year'] = $academic_year;
                $arr[$key]['date'] = $s['date'];
            }

            foreach($arr as $key=>$a){
                array_push($data['data'],$a);
            }
            $data['total_data'] = $data['total_data'] + count($arr);
        }

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
            'module_id' => 'required',
            'test_type' => 'required',
        ]);
        // test_type have to be pretest/journal/posttest
        $module = Module::find($request->module_id);
        if(!$module) {
            return $this->response->errorNotFound('invalid module id');
        }

        $questions = $request->questions;

        $test = Test::create([
            'type' => $request->type,
        ]);

        if($request->has('file')) {
            $file = $request->file('file');
            $filename = time().str_replace(" ", "",$file->getClientOriginalName());
            $weight = $request->weight;
            $get_answer = $request->answer;

            $question = Question::create([
                'test_id' => $test->id,
                'question' => $filename,
                'weight' => $weight,
            ]);
            $answer = Answer::create([
                'question_id' => $question['id'],
                'answer' => $get_answer,
            ]);

            $file->storeAs('Journal', $filename);
        } else {
            $questions = $this->add_question($request, $test->id);
        }

        if($request->test_type === 'pretest') {
            if($module->pretest_id == null) {
                $modules = Module::where('course_id', $module->course_id)
                                ->where('index', $module->index)
                                ->select('id')
                                ->get();
                $ids = [];
                foreach($modules as $m) {
                    array_push($ids, $m['id']);
                }

                foreach($ids as $id) {
                    $module = Module::find($id);
                    $module->update(['pretest_id' => $test->id]);
                    $module->save();
                }
            } else {
                $module->update(['pretest_id' => $test->id]);
                $module->save();
            }
        } else if($request->test_type === 'journal') {
            if($module->journal_id == null) {
                $modules = Module::where('course_id', $module->course_id)
                                ->where('index', $module->index)
                                ->select('id')
                                ->get();
                $ids = [];
                foreach($modules as $m) {
                    array_push($ids, $m['id']);
                }

                foreach($ids as $id) {
                    $module = Module::find($id);
                    $module->update(['journal_id' => $test->id]);
                    $module->save();
                }
            } else {
                $module->update(['journal_id' => $test->id]);
                $module->save();
            }
        } else if($request->test_type === 'posttest') {
            if($module->posttest_id == null) {
                $modules = Module::where('course_id', $module->course_id)
                                ->where('index', $module->index)
                                ->select('id')
                                ->get();
                $ids = [];
                foreach($modules as $m) {
                    array_push($ids, $m['id']);
                }

                foreach($ids as $id) {
                    $module = Module::find($id);
                    $module->update(['posttest_id' => $test->id]);
                    $module->save();
                }
            } else {
                $module->update(['posttest_id' => $test->id]);
                $module->save();
            }
        } else {
            return $this->response->errorBadRequest('invalid test type');
        }

        return $this->response->item($test, TestTransformer::class);
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
                    'weight' => $q['weight'],
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
                    'weight' => $q['weight'],
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

    public function update_question(Request $request, $id) {
        $this->validate($request, [
            'question' => 'required',
        ]);
        $question = Question::find($id);
        if(!$question) {
            return $this->response->errorNotFound('invalid question id');
        }

        $question->update(['question' => $request->question]);
        $question->save();

        return $this->response->noContent();
    }

    public function delete_question(Request $request, $id) {
        $question = Question::find($id);
        if(!$question) {
            return $this->response->errorNotFound('invalid question id');
        }

        $question->delete();

        return $this->response->noContent();
    }

    public function update_answer(Request $request, $id) {
        $this->validate($request, [
            'answer' => 'required',
        ]);
        $answer = Answer::find($id);
        if(!$answer) {
            return $this->response->errorNotFound('invalid answer id');
        }

        $answer->update(['answer' => $request->answer]);
        if($request->has('is_answer')) {
            $answer->update(['is_answer' => $request->is_answer]);
        }
        $answer->save();

        return $this->response->noContent();
    }

    public function delete_answer(Request $request, $id) {
        $answer = Answer::find($id);
        if(!$answer) {
            return $this->response->errorNotFound('invalid answer id');
        }

        $answer->delete();

        return $this->response->noContent();
    }

    public function get_student_class_course(Request $request, $student_id) {
        $class_course_in_scc = StudentClassCourse::where('student_id',$student_id)->get();
        if($class_course_in_scc != NULL) {
            $data = [];
            foreach($class_course_in_scc as $key=>$cc_scc) {
                $class_course = ClassCourse::find($cc_scc->class_course_id);
                $course = $class_course->courses;
                $class = $class_course->classes;
                $staff = $class_course->staffs;
                $academic_year = $class_course->academic_years;
                $academic_year = AcademicYear::firstWhere('id', $class_course->academic_year_id);

                $data['data'][$key]['class_course_id'] = $class_course->id;
                $data['data'][$key]['class_id'] = $class->id;
                $data['data'][$key]['class_name'] = $class->name;
                $data['data'][$key]['course_id'] = $course->id;
                $data['data'][$key]['course_code'] = $course->code;
                $data['data'][$key]['course_name'] = $course->name;
                $data['data'][$key]['staff_code'] = $staff->code;
                $data['data'][$key]['staff_name'] = $staff->name;
                $data['data'][$key]['academic_year'] = $academic_year->year.' / '.$academic_year->semester;
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

    public function show(Request $request, $id)
    {
        $schedule = Schedule::where('id', $id)->first();
        $arr = null;

        if($schedule != null){
            $class_course = ClassCourse::where('id', $schedule['class_course_id'])->first();
            $class = Classroom::where('id', $class_course['class_id'])->first();
            $course = Course::where('id', $class_course['course_id'])->first();
            $staff = Staff::where('id', $class_course['staff_id'])->first();
            $class_academic_year = Staff::where('id', $class_course['staff_id'])->first();
            $academic_year = AcademicYear::where('id', $schedule['academic_year_id'])->first();
            $module = Module::where('id', $schedule['module_id'])->first();
            $room = Room::where('id', $schedule['room_id'])->first();

            $arr['id'] = $schedule['id'];
            $arr['title'] = $schedule['name'];
            $arr['start'] = $schedule['time_start'];
            $arr['end'] = $schedule['time_end'];
            $arr['room'] = $room;
            $arr['class_course']['id'] = $class_course['id'];
            $arr['class_course']['class']['id'] = $class['id'];
            $arr['class_course']['class']['name'] = $class['name'];
            $arr['class_course']['class']['academic_year'] = $class['academic_year'];
            $arr['class_course']['course']['id'] = $course['id'];
            $arr['class_course']['course']['name'] = $course['name'];
            $arr['class_course']['course']['code'] = $course['code'];
            $arr['class_course']['staff']['id'] = $staff['id'];
            $arr['class_course']['staff']['name'] = $staff['name'];
            $arr['class_course']['staff']['code'] = $staff['code'];
            $arr['module'] = $module;
            $arr['academic_year'] = $academic_year;
            $arr['date'] = $schedule['date'];
        }

        $data['data'] = $arr;
        return $data;

    }

    public function show_schedule(Request $request, $class_course_id){
        $schedules = Schedule::Where('class_course_id', $class_course_id)->get();

        if($request->has('module')) {
            if(!empty($request->module)) {
                $module = Module::where('index', $request->module)->get();
                $x = [];
                foreach($module as $key=>$m) {
                    $x[$key] = $m->id;
                }
                $schedules = $schedules->whereIn('module_id', $x);
            }
        }

        $data['total_data'] = 0;
        $data['data'] = [];
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
            $arr[$key]['class_course']['course']['code'] = $course['code'];
            $arr[$key]['class_course']['staff']['id'] = $staff['id'];
            $arr[$key]['class_course']['staff']['name'] = $staff['name'];
            $arr[$key]['class_course']['staff']['code'] = $staff['code'];
            $arr[$key]['module'] = $module;
            $arr[$key]['academic_year'] = $academic_year;
            $arr[$key]['date'] = $s['date'];
        }

        foreach($arr as $key=>$a){
            array_push($data['data'],$a);
        }
        $data['total_data'] = $data['total_data'] + count($arr);

        return $data;
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
