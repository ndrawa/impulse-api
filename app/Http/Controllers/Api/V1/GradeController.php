<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Question;
use App\Models\Test;
use App\Models\StudentEssayAnswer;
use App\Models\StudentMultipleChoiceAnswer;
use App\Models\Answer;
use App\Models\User;
use App\Transformers\AnswerEssayTransformer;
use App\Transformers\AnswerMultipleChoiceTransformer;
use App\Transformers\UserTransformer;

class GradeController extends BaseController
{
    public function getMeGrades(Request $request) {
        $student = $this->user->student;
        $user_id = $this->user->id;
        $student_class_course = $student->student_class_course;

        $data = [];
        foreach($student_class_course as $key=>$scc) {
            $data['data']['class_course'][$key]['schedule'] = $student_class_course[$key]->class_course->schedule;
        }
        return $data;
    }
}
