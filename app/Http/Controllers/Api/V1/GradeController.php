<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Question;
use App\Models\Test;
use App\Models\StudentEssayAnswer;
use App\Models\StudentMultipleChoiceAnswer;
use App\Models\Answer;
use App\Models\User;
use App\Models\Module;
use App\Models\Schedule;
use App\Models\StudentClassCourse;
use App\Models\ClassCourse;
use App\Models\ScheduleTest;
use App\Transformers\AnswerEssayTransformer;
use App\Transformers\AnswerMultipleChoiceTransformer;
use App\Transformers\GradeTransformer;
use App\Transformers\ScheduleTransformer;
use App\Transformers\UserTransformer;

class GradeController extends BaseController
{
    public function getStudentGrades(Request $request, $student_id, $course_id = null) {
        $student = Student::find($student_id);
        if(!$student) {
            return $this->response->errorNotFound('invalid student id');
        }
        $student_class_courses = StudentClassCourse::where('student_id', $student_id)->get();
        $class_courses = [];
        if($course_id != null) {
            $class_course_ids = ClassCourse::select('class_course.id as class_course_id', 'courses.name as course', 'classes.name as class')
                                            ->where('class_course.course_id', $course_id)
                                            ->join('courses','courses.id', '=', 'class_course.course_id')
                                            ->join('classes','classes.id', '=', 'class_course.class_id')
                                            ->get()
                                            ->toArray();
            foreach($class_course_ids as $cc_id) {
                foreach($student_class_courses as $val) {
                    if($cc_id['class_course_id'] == $val['class_course_id']) {
                        $class_course = $cc_id;
                        array_push($class_courses, $class_course);
                    }
                }
            }
        } else {
            foreach($student_class_courses as $val) {
                $class_course = ClassCourse::select('class_course.id as class_course_id', 'courses.name as course', 'classes.name as class')
                                        ->where('class_course.id',$val['class_course_id'])
                                        ->join('courses','courses.id', '=', 'class_course.course_id')
                                        ->join('classes','classes.id', '=', 'class_course.class_id')
                                        ->first()
                                        ->toArray();
                array_push($class_courses, $class_course);
            }
        }

        $class_course_schedules = [];
        foreach($class_courses as $key=>$val) {
            $schedule = Schedule::select('id','name','module_id')
                                ->where('class_course_id', $val['class_course_id'])
                                ->get()
                                ->toArray();;
            array_push($class_course_schedules, $schedule);
        }

        $module_test = [];
        foreach($class_course_schedules as $cc_key=>$class_course) {
            if(sizeof($class_course) == 0) {
                $module_test[$cc_key] = [];
                continue;
            }
            foreach($class_course as $s_key=>$schedule) {
                $pretest = Module::select('pretest_id')
                                ->where('id', $schedule['module_id'])
                                ->get()
                                ->toArray();
                if($pretest[0]['pretest_id'] == null) {
                    $pretest_grade = 0;
                } else {
                    $pretest_grade = $this->getStudentTestGrade($request, $student_id, $pretest[0]['pretest_id'])['data']['total_grade'];
                }

                $journal = Module::select('journal_id')
                                ->where('id', $schedule['module_id'])
                                ->get()
                                ->toArray();
                if($journal[0]['journal_id'] == null) {
                    $journal_grade = 0;
                } else {
                    $journal_grade = $this->getStudentTestGrade($request, $student_id, $journal[0]['journal_id'])['data']['total_grade'];
                }

                $posttest = Module::select('posttest_id')
                                ->where('id', $schedule['module_id'])
                                ->get()
                                ->toArray();
                if($posttest[0]['posttest_id'] == null) {
                    $posttest_grade = 0;
                } else {
                    $posttest_grade = $this->getStudentTestGrade($request, $student_id, $posttest[0]['posttest_id'])['data']['total_grade'];
                }

                $index = Module::select('index')
                            ->where('id', $schedule['module_id'])
                            ->get()
                            ->toArray();

                $module_test[$cc_key]['modules'][$s_key]['index'] =  $index[0]['index'];
                $module_test[$cc_key]['modules'][$s_key]['pretest_grade'] =  $pretest_grade;
                $module_test[$cc_key]['modules'][$s_key]['journal_grade'] =  $journal_grade;
                $module_test[$cc_key]['modules'][$s_key]['posttest_grade'] =  $posttest_grade;

            }
        }

        foreach($class_courses as $key=>$val) {
            $class_courses[$key]['schedules'] = $class_course_schedules[$key];
            foreach($class_courses[$key]['schedules'] as $s_key=>$schedule)  {
                $class_courses[$key]['schedules'] = $module_test[$key];
            }
        }

        return json_encode($class_courses);
    }

    public function getStudentTestGrade(Request $request, $student_id, $test_id) {
        $student = Student::find($student_id);
        $test = Test::find($test_id);

        if(!$student or !$test) {
            return $this->response->errorNotFound('invalid student id or test id');
        }
        $q = $test->questions;

        $q_ids = [];
        foreach($q as $key=>$val) {
            array_push($q_ids, $val['id']);
        }
        $student_grade = Grade::whereIn('question_id', $q_ids)->get();
        $total_grade = 0;
        foreach($student_grade as $val) {
            $total_grade = $total_grade + $val['grade'];
        }

        $data['student'] = $this->user->student;
        foreach($student_grade as $key=>$val) {
            $data['grade'][$key]['id'] = $val['id'];
            $data['grade'][$key]['schedule_test_id'] = $val['schedule_test_id'];
            $question = Question::find($val['question_id']);
            $data['grade'][$key]['question'] = $question;
            $data['grade'][$key]['question']['answers'] = $question->answers;
            $data['grade'][$key]['grade'] = $val['grade'];
            $total_grade = $total_grade + $val['grade'];
            $data['grade'][$key]['asprak'] = Student::find($val['asprak_id']);
            if($test->type == 'multiple_choice') {
                $student_answer = StudentMultipleChoiceAnswer::where('question_id', $question->id)
                                                            ->where('student_id', $this->user->student->id)
                                                            ->get();
            } else if($test->type == 'essay') {
                $student_answer = StudentEssayAnswer::where('question_id', $question->id)
                                                            ->where('student_id', $this->user->student->id)
                                                            ->first();
            }
            $data['grade'][$key]['student_answer'] = $student_answer;
        }
        $data['total_grade'] = $total_grade;

        $arr_return['data'] = $data;
        return $arr_return;
    }
}
