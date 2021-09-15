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

use Illuminate\Support\Arr;

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
            $class_course_ids = ClassCourse::select('class_course.id as class_course_id', 'courses.name as course', 'classes.name as class', 'staffs.code as staff', 'academic_years.year as year', 'academic_years.semester as semester')
                                            ->where('class_course.course_id', $course_id)
                                            ->join('courses','courses.id', '=', 'class_course.course_id')
                                            ->join('classes','classes.id', '=', 'class_course.class_id')
                                            ->join('staffs','staffs.id', '=', 'class_course.staff_id')
                                            ->join('academic_years','academic_years.id', '=', 'class_course.academic_year_id')
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
                $class_course = ClassCourse::select('class_course.id as class_course_id', 'courses.name as course', 'classes.name as class', 'staffs.code as staff', 'academic_years.year as year', 'academic_years.semester as semester')
                                        ->where('class_course.id',$val['class_course_id'])
                                        ->join('courses','courses.id', '=', 'class_course.course_id')
                                        ->join('classes','classes.id', '=', 'class_course.class_id')
                                        ->join('staffs','staffs.id', '=', 'class_course.staff_id')
                                        ->join('academic_years','academic_years.id', '=', 'class_course.academic_year_id')
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
                    $pretest_submitted = false;
                } else {
                    $pretest_grade = $this->getStudentTestGrade($request, $student_id, $pretest[0]['pretest_id'])['data']['total_grade'];
                    $pretest_submitted = true;
                }

                $journal = Module::select('journal_id')
                                ->where('id', $schedule['module_id'])
                                ->get()
                                ->toArray();
                if($journal[0]['journal_id'] == null) {
                    $journal_grade = 0;
                    $journal_submitted = false;
                } else {
                    $journal_grade = $this->getStudentTestGrade($request, $student_id, $journal[0]['journal_id'])['data']['total_grade'];
                    $journal_submitted = true;
                }

                $posttest = Module::select('posttest_id')
                                ->where('id', $schedule['module_id'])
                                ->get()
                                ->toArray();
                if($posttest[0]['posttest_id'] == null) {
                    $posttest_grade = 0;
                    $posttest_submitted = false;
                } else {
                    $posttest_grade = $this->getStudentTestGrade($request, $student_id, $posttest[0]['posttest_id'])['data']['total_grade'];
                    $posttest_submitted = true;
                }

                $index = Module::select('index')
                            ->where('id', $schedule['module_id'])
                            ->get()
                            ->toArray();

                $module_test[$cc_key][$s_key]['index'] =  $index[0]['index'];
                $module_test[$cc_key][$s_key]['pretest_grade'] =  $pretest_grade;
                $module_test[$cc_key][$s_key]['journal_grade'] =  $journal_grade;
                $module_test[$cc_key][$s_key]['posttest_grade'] =  $posttest_grade;

            }
        }

        foreach($class_courses as $key=>$val) {
            $class_courses[$key]['modules'] = $class_course_schedules[$key];
            foreach($class_courses[$key]['modules'] as $s_key=>$schedule)  {
                $class_courses[$key]['modules'] = $module_test[$key];
            }
        }
        $data['data']['student'] = $student;
        $data['data']['result'] = $class_courses;
        return json_encode($data);
    }

    public function getStudentTestGrade(Request $request, $student_id, $test_id) {
        $student = Student::find($student_id);
        if(!$student) {
            return $this->response->errorNotFound('invalid student id');
        }
        if($test_id != null) {
            $test = Test::find($test_id);

            if(!$test) {
                return $this->response->errorNotFound('invalid test id');
            }
            $q = $test->questions;

            $q_ids = [];
            foreach($q as $key=>$val) {
                array_push($q_ids, $val['id']);
            }
            $student_grade = Grade::whereIn('question_id', $q_ids)
                                    ->where('student_id', $student->id)
                                    ->get();
            if(count($student_grade) > 0) {
                $data['submitted'] = true;
            } else {
                $data['submitted'] = false;
            }
            $total_grade = 0;
            // foreach($student_grade as $val) {
            //     $total_grade = $total_grade + $val['grade'];
            // }
            $data['total_grade'] = 0;

            $data['test'] = $test;
            $data['question'] = $test->questions;
            foreach($data['question'] as $key=>$q) {
                $question = $data['question'][$key];
                $data['question'][$key]['answers'] = $question->answers;
            }
            unset($data['question']);

            $data['student'] = $student;
            foreach($student_grade as $key=>$val) {
                $data['grade'][$key]['id'] = $val['id'];
                $data['grade'][$key]['schedule_test_id'] = $val['schedule_test_id'];
                $data['grade'][$key]['grade'] = $val['grade'];
                $data['grade'][$key]['asprak'] = Student::find($val['asprak_id']);
                $question = Question::find($val['question_id']);
                $data['grade'][$key]['question'] = $question;
                $data['grade'][$key]['question']['answers'] = $question->answers;
                $total_grade = $total_grade + $val['grade'];
                if($test->type == 'multiple_choice') {
                    $student_answer = StudentMultipleChoiceAnswer::where('question_id', $question->id)
                                                                ->where('student_id', $student->id)
                                                                ->get();
                } else if($test->type == 'essay') {
                    $student_answer = StudentEssayAnswer::where('question_id', $question->id)
                                                                ->where('student_id', $student->id)
                                                                ->first();
                }
                $data['grade'][$key]['student_answer'] = $student_answer;
            }

            $data['total_grade'] = $total_grade;

        } else {
            $data['total_grade'] = 'null test';
            $data['submitted'] = false;
        }

        $arr_return['data'] = $data;
        return $arr_return;
    }

    // public function getClassCourseGrade(Request $request, $class_course_id, $module_index) {
    //     $class_course = ClassCourse::find($class_course_id);
    //     if(!$class_course) {
    //         return $this->response->errorNotFound('invalid class course id');
    //     }
    //     if($module_index < 1 or $module_index > 14) {
    //         return $this->response->errorBadRequest('index out of bonds');
    //     }
    //     $class_course_students = $class_course->student->toArray();

    //     $schedules = Schedule::where('class_course_id', $class_course_id)->get()->toArray();
    //     foreach($schedules as $key=>$val) {
    //         $module = Module::find($val['module_id']);
    //         if($module->index == $module_index) {
    //             break;
    //         }
    //     }

    //     $students = array();
    //     foreach($class_course_students as $key=>$val) {
    //         $stud = Student::find($val['student_id']);
    //         $students[$key] = [
    //             'id' => $stud->id,
    //             'nim' => $stud->nim,
    //             'name' => $stud->name,
    //             'test_id' => [
    //                 'pretest_id' => $module['pretest_id'],
    //                 'journal_id' => $module['journal_id'],
    //                 'posttest_id' => $module['posttest_id'],
    //             ],
    //             'grade' => [
    //                 'pretest' => $this->getStudentTestGrade($request, $stud->id, $module['pretest_id'])['data']['total_grade'],
    //                 'journal' => $this->getStudentTestGrade($request, $stud->id, $module['journal_id'])['data']['total_grade'],
    //                 'posttest' => $this->getStudentTestGrade($request, $stud->id, $module['posttest_id'])['data']['total_grade'],
    //             ]
    //         ];
    //     }

    //     $data['data'] = $students;
    //     return json_encode($data);
    // }

    public function getScheduleGrade(Request $request, $schedule_id) {
        $schedule = Schedule::find($schedule_id);
        if(!$schedule) {
            return $this->response->errorNotFound('invalid schedule id');
        }

        $module = $schedule->module;
        $class_course_students = $schedule->class_course->student;

        $students = array();
        foreach($class_course_students as $key=>$val) {
            $stud = Student::find($val['student_id']);
            $pretest = $this->getStudentTestGrade($request, $stud->id, $module['pretest_id']);
            $journal = $this->getStudentTestGrade($request, $stud->id, $module['journal_id']);
            $posttest = $this->getStudentTestGrade($request, $stud->id, $module['posttest_id']);
            // return $pretest['data']['submitted'];
            $students[$key] = [
                'id' => $stud->id,
                'nim' => $stud->nim,
                'name' => $stud->name,
                'test_id' => [
                    'pretest_id' => $module['pretest_id'],
                    'journal_id' => $module['journal_id'],
                    'posttest_id' => $module['posttest_id'],
                ],
                'grade' => [
                    'pretest' => $pretest['data']['total_grade'],
                    'journal' => $journal['data']['total_grade'],
                    'posttest' => $posttest['data']['total_grade'],
                ],
                'submitted' => [
                    'pretest' => $pretest['data']['submitted'],
                    'journal' => $journal['data']['submitted'],
                    'posttest' => $posttest['data']['submitted'],
                ]
            ];
        }
        $data['data'] = $students;
        return $data;
    }

    public function asprakUpdateGrade(Request $request, $student_id) {
        $this->validate($request, [
            'grade' => 'required',
        ]);
        foreach($request->grade as $val) {
            $grade = Grade::where('student_id', $student_id)
                        ->where('question_id', $val['question_id'])
                        ->first();
            $grade->update([
                'grade' => $val['grade'],
                'asprak_id' => $this->user->student->id
            ]);
            $grade->save();
        }

        return $this->response->noContent();
    }
}
