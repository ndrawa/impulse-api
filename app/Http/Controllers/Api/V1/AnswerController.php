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
use App\Models\User;
use App\Transformers\AnswerEssayTransformer;
use App\Transformers\AnswerMultipleChoiceTransformer;
use App\Transformers\UserTransformer;

class AnswerController extends BaseController
{
    public function getStudentEssayAnswer(Request $request, $test_id, $user_id) {
        $test = Test::find($test_id);
        $user = User::find($user_id);
        if(!$test or !$user) {
            return $this->response->errorNotFound('invalid test id or user id');
        }
        if($test->type != 'essay') {
            return $this->response->errorNotFound('invalid test id');
        }
        $questions = $test->questions;
        $question_ids = [];
        foreach($questions as $q) {
            array_push($question_ids, $q->id);
        }
        $student_answer = StudentEssayAnswer::whereIn('question_id', $question_ids)
                                            ->where('user_id', $user_id)
                                            ->get();

        $data = [];
        $data['data']['student'] = $user->student;
        $data['data']['test']['id'] = $test->id;
        $data['data']['test']['type'] = $test->type;
        foreach($student_answer as $key=>$ans) {
            $question = Question::find($ans->question_id);
            $data['data']['answer'][$key]['id'] = $ans->id;
            $data['data']['answer'][$key]['question']['id'] = $question->id;
            $data['data']['answer'][$key]['question']['question'] = $question->question;
            $data['data']['answer'][$key]['answers'] = $ans->answers;
        }

        return $data;
    }

    public function getStudentMultipleChoiceAnswer(Request $request, $test_id, $user_id) {
        $test = Test::find($test_id);
        $user = User::find($user_id);

        if(!$test or !$user) {
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
                                                    ->where('user_id', $user_id)
                                                    ->get();

        $data = [];
        $data['data']['student'] = $user->student;
        $data['data']['test']['id'] = $test->id;
        $data['data']['test']['type'] = $test->type;
        foreach($student_answer as $key=>$ans) {
            $question = Question::find($ans->question_id);
            $answer = Answer::find($ans->answer_id);
            $data['data']['answer'][$key]['id'] = $ans->id;
            $data['data']['answer'][$key]['question']['id'] = $question->id;
            $data['data']['answer'][$key]['question']['question'] = $question->question;
            $data['data']['answer'][$key]['answer']['id'] = $answer->id;
            $data['data']['answer'][$key]['answer']['answer'] = $answer->answer;
            $data['data']['answer'][$key]['answer']['is_answer'] = $answer->is_answer;
            $data['data']['answer'][$key]['answer']['question_id'] = $answer->question_id;
        }
        $data['data']['result'] = $this->getMultipleChoiceGrade($request, $test_id, $user_id);

        return $data;
    }

    public function getMultipleChoiceGrade(Request $request, $test_id, $user_id) {
        $test = Test::find($test_id);
        $user = User::find($user_id);

        if(!$test or !$user) {
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
                                                    ->where('user_id', $user_id)
                                                    ->get();

        $q_total = count($student_answer);
        $correct = 0;
        $wrong = 0;
        foreach($student_answer as $ans) {
            $answer = Answer::find($ans->answer_id);
            if($answer->is_answer) {
                $correct = $correct + 1;
            } else {
                $wrong = $wrong + 1;
            }
        }
        $data['correct'] = $correct;
        $data['wrong'] = $wrong;
        $data['question_total'] = $q_total;
        if($q_total != 0) {
            $data['grade'] = $correct / $q_total * 100.0;;
        } else {
            $data['grade'] = 0;
        }
        return $data;

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
            if(StudentEssayAnswer::where('user_id', $this->user->id)
                                ->where('question_id', $answer['question_id'])
                                ->exists())
            {
                continue;
            }
            StudentEssayAnswer::create([
                'question_id' => $answer['question_id'],
                'answers' => $answer['answers'],
                'user_id' => $this->user->id,
            ]);
        }

        return $this->response->created();
    }

    public function StoreMultipleChoiceAnswer(Request $request)
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
            if(StudentMultipleChoiceAnswer::where('user_id', $this->user->id)
                                ->where('question_id', $answer['question_id'])
                                ->exists())
            {
                continue;
            }

            StudentMultipleChoiceAnswer::create([
                'question_id' => $answer['question_id'],
                'answer_id' => $answer['answer_id'],
                'user_id' => $this->user->id,
            ]);

            //$a is object of Answer
            //$q is object of question, retrieved from $a
            $a = Answer::find($answer['answer_id']);
            $q = $a->questions;
            //$q_answer is a collection of answer from the $q (list all choices)
            $q_answer = $q->answers;
            //$a_true will collect the answers with is_answer = true
            $a_true = [];
            foreach($q_answer as $a_t) {
                if($t['is_answer'] == 'true') {
                    $array_push($a_true,$a_t['id']);
                }
            }


            if(count($a_true) > 1) {
                //If question has more than one is_answer = true answer
                $per_q_weight = $q->weight / count($a_true);
            } else {
                $per_q_weight = $q->weight;
            }

            if(in_array($answer['answer_id'], $a_true)) {
                $grade = $per_q_weight;
            } else {
                $grade = 0.0;
            }

            //Creating a row of grade
            $grade = Grade::create([
                'student_id' => $this->user->id,
                'question_id' => $answer['question_id'],
                'schedule_test_id' => $request->schedule_test_id,
                'grade' => $grade,
            ]);
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
