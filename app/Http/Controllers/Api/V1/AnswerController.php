<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Question;
use App\Models\Test;
use App\Models\StudentEssayAnswer;
use App\Models\StudentMultipleChoiceAnswer;
use App\Models\Answer;
use App\Models\Grade;
use App\Models\Student;
use App\Transformers\AnswerEssayTransformer;
use App\Transformers\AnswerMultipleChoiceTransformer;
use App\Transformers\UserTransformer;

class AnswerController extends BaseController
{
    public function getStudentEssayAnswer(Request $request, $test_id, $student_id) {
        $test = Test::find($test_id);
        $student = Student::find($student_id);
        if(!$test or !$student) {
            return $this->response->errorNotFound('invalid test id or student id');
        }
        if($test->type != 'essay' && $test->type != 'file') {
            return $this->response->errorNotFound('invalid test id');
        }
        $questions = $test->questions;
        $question_ids = [];
        foreach($questions as $q) {
            array_push($question_ids, $q->id);
        }
        $student_answer = StudentEssayAnswer::whereIn('question_id', $question_ids)
                                            ->where('student_id', $student_id)
                                            ->get();

        $data = [];
        $data['data']['student'] = $student;
        $data['data']['test']['id'] = $test->id;
        $data['data']['test']['type'] = $test->type;
        foreach($student_answer as $key=>$ans) {
            $question = Question::find($ans->question_id);
            $data['data']['student_answer'][$key]['id'] = $ans->id;
            $data['data']['student_answer'][$key]['question']['id'] = $question->id;
            $data['data']['student_answer'][$key]['question']['question'] = $question->question;
            $data['data']['student_answer'][$key]['question']['weight'] = $question->weight;
            $data['data']['student_answer'][$key]['answer'] = $ans->answers;
        }

        return $data;
    }

    public function getStudentMultipleChoiceAnswer(Request $request, $test_id, $student_id) {
        $test = Test::find($test_id);
        $student = Student::find($student_id);

        if(!$test or !$student) {
            return $this->response->errorNotFound('invalid test id or user id');
        }
        if($test->type != 'multiple_choice') {
            return $this->response->errorNotFound('invalid test id');
        }
        $questions = $test->questions;
        $question_ids = [];
        foreach($questions as $q) {
            array_push($question_ids, $q->id);
        }
        $student_answer = StudentMultipleChoiceAnswer::whereIn('question_id', $question_ids)
                                                    ->where('student_id', $student_id)
                                                    ->get();

        $data = [];
        $data['data']['student'] = $student;
        $data['data']['test']['id'] = $test->id;
        $data['data']['test']['type'] = $test->type;
        foreach($student_answer as $key=>$ans) {
            $question = Question::find($ans->question_id);
            $data['data']['student_answer'][$key]['id'] = $ans->id;
            $data['data']['student_answer'][$key]['question']['id'] = $question->id;
            $data['data']['student_answer'][$key]['question']['question'] = $question->question;
            $data['data']['student_answer'][$key]['question']['weight'] = $question->weight;
            $data['data']['student_answer'][$key]['question']['answers'] = $question->answers;
            if($ans->answer_id != null)  {
                $answer = Answer::find($ans->answer_id);
                $data['data']['student_answer'][$key]['answer']['id'] = $answer->id;
                $data['data']['student_answer'][$key]['answer']['answer'] = $answer->answer;
                $data['data']['student_answer'][$key]['answer']['is_answer'] = $answer->is_answer;
            } else {
                $data['data']['student_answer'][$key]['answer']['id'] = null;
                $data['data']['student_answer'][$key]['answer']['answer'] = null;
                $data['data']['student_answer'][$key]['answer']['is_answer'] = false;
            }
        }

        return $data;
    }

    public function getAnswerGrade(Request $request, $test_id, $student_id) {
        $test = Test::find($test_id);
        $student = Student::find($student_id);

        if(!$test or !$student) {
            return $this->response->errorNotFound('invalid test id or user id');
        }

        if($test->type == 'essay') {
            $data = $this->getStudentEssayAnswer($request, $test_id, $student_id);
        } else if($test->type == 'multiple_choice') {
            $data = $this->getStudentMultipleChoiceAnswer($request, $test_id, $student_id);
            return $data;
            foreach($data['data']['student_answer'] as $key=>$val) {
                $data['data']['student_answer'][$key]['grade'] = 100;
            }
            return $data;
        }
    }

    public function StoreEssayAnswer(Request $request)
    {
        $this->validate($request, [
            'schedule_test_id' => 'required',
            'answers' => 'required',
        ]);
        if(!is_array($request->answers) or sizeof($request->answers) < 1) {
            return $this->response->errorBadRequest();
        }
        foreach($request->answers as $answer) {
            //Kalau memenuhi kondisi, skip ke iterasi selanjutnya
            if(StudentEssayAnswer::where('student_id', $this->user->student->id)
                                ->where('question_id', $answer['question_id'])
                                ->exists())
            {
                continue;
            }
            StudentEssayAnswer::create([
                'question_id' => $answer['question_id'],
                'answers' => $answer['answers'],
                'student_id' => $this->user->student->id,
            ]);

            Grade::create([
                'student_id' => $this->user->student->id,
                'question_id' => $answer['question_id'],
                'schedule_test_id' => $request->schedule_test_id,
            ]);
            //grade column will automatically set to 0.0


        }

        return $this->response->created();
    }

    public function StoreMultipleChoiceAnswer(Request $request)
    {
        $this->validate($request, [
            'schedule_test_id' => 'required',
            'questions' => 'required',
        ]);

        foreach($request->questions as $val) {
            $answered_question_check = StudentMultipleChoiceAnswer::where('student_id', $this->user->student->id)
                                                            ->where('question_id', $val['id'])
                                                            ->first();
            if($answered_question_check) {
                continue;
            }
            if($val['answers'][0] != '') {
                $question = Question::find($val['id']);
                $question_answers = Answer::where('question_id', $val['id'])->get();
                $a_true = [];
                foreach($question_answers as $ans)  {
                    if($ans['is_answer'] == true) {
                        array_push($a_true, $ans['id']);
                    }
                }

                //Membuat baris di student_answers_multiple_choice
                //Sekalian cari jawaban praktikan yang benar berdasarkan array $a_true
                $student_answer_true = [];
                foreach($val['answers'] as $student_answer) {
                    StudentMultipleChoiceAnswer::create([
                        'question_id' => $val['id'],
                        'answer_id' => $student_answer,
                        'student_id' => $this->user->student->id,
                    ]);
                    if(in_array($student_answer, $a_true)) {
                        array_push($student_answer_true, $student_answer);
                    }
                }

                //Untuk antisipasi case :
                //Misal opsi jawaban soal no. 1 adalah A B C D E
                //Opsi yang benar (is_answer) adalah B
                //Sedangkan opsi yang dipilih praktikan adalah A B C E
                //Maka akan masuk ke baris else
                //Apabila misal opsi yang benar adalah A B
                //Sedangkan opsi yang dipilih praktikan adalah B C
                //Maka seharusnya akan masuk baris if dan karena tidak sesuai, grade = 0
                if(count($val['answers']) == count($a_true)) {
                    $val_answers = sort($val['answers']);
                    sort($student_answer_true);
                    if($val_answers == $student_answer_true) {
                        $grade = $question->weight;
                    } else {
                        $grade = 0;
                    }
                } else {
                    $grade = 0;
                }

                // $count_is_answer = count($a_true);
                // $count_student_choices = count($val['answers']);
                // $count_student_true = count($student_answer_true);
                // $grade = $count_student_true / $count_student_choices * $question->weight;

                Grade::create([
                    'student_id' => $this->user->student->id,
                    'question_id' => $val['id'],
                    'schedule_test_id' => $request->schedule_test_id,
                    'grade' => $grade,
                    'asprak_id' => null
                ]);
            } else {
                StudentMultipleChoiceAnswer::create([
                    'question_id' => $val['id'],
                    'answer_id' => null,
                    'student_id' => $this->user->student->id,
                ]);

                Grade::create([
                    'student_id' => $this->user->student->id,
                    'question_id' => $val['id'],
                    'schedule_test_id' => $request->schedule_test_id,
                    'grade' => 0,
                    'asprak_id' => null
                ]);
            }

        }

        return $this->response->created();
    }

    public function updateEssayAnswer(Request $request)
    {
        $this->validate($request, [
            'test_id' => 'required',
            'answers' => 'required',
        ]);
        if(!is_array($request->answers) or sizeof($request->answers) < 1) {
            return $this->response->errorBadRequest();
        }

        foreach($request->answers as $answer) {
            $data_answer = StudentEssayAnswer::find($answer['id']);
            $data_answer->update(['answers' => $answer['answers']]);
            $data_answer->save;
        }

        return $this->response->noContent();
    }

    public function updateMultipleChoiceAnswer(Request $request)
    {
        $this->validate($request, [
            'test_id' => 'required',
            'answers' => 'required',
        ]);
        if(!is_array($request->answers) or sizeof($request->answers) < 1) {
            return $this->response->errorBadRequest();
        }
        foreach($request->answers as $answer) {
            $data_answer = StudentMultipleChoiceAnswer::find($answer['id']);
            $data_answer->update(['answer_id' => $answer['answer_id']]);
            $data_answer->save;
        }

        return $this->response->noContent();
    }
}
