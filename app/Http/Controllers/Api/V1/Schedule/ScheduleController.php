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
use App\Transformers\ScheduleTransformer;
use App\Transformers\TestTransformer;
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

    public function import(Request $request) {
        Excel::import(new ScheduleImport, request()->file('file'));

        return 'import success';
    }

    public function create(Request $request)
    {
        // $this->authorize('create', $this->user());
        $this->validate($request, [
            'name' => 'required',
            'day' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'room_id' => 'required',
            // 'periode_start' => 'required',
            // 'periode_end' => 'required',
            'class_id' => 'required',
            'module_id' => 'required',
            'type' => 'required',
        ]);
        $schedule = Schedule::create($request->all());

        return $this->response->item($schedule, new ScheduleTransformer);
    }

    public function getTest(Request $request) {
        $test = Test::firstWhere('id', $request->id);

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

        return $test;
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
}
