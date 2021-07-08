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
            'time_start' => 'required',
            'time_end' => 'required',
        ]);

        $test = Test::create([
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
        ]);


        foreach($request->questions as $key => $q) {

            //insert question
            $qstion = Question::create([
                'test_id' => $test->id,
                'type' => $q['type'],
                'question' => $q['text'],
            ]);

            //insert answers based to question type
            if($q['type'] == 'essay') {
                $answer = Answer::create([
                    'question_id' => $qstion->id,
                    'answer' => $q['answers'],
                ]);
            } else {
                for($i=0; $i < count($q['answers']['answer']); $i++) {
                    $ans = Answer::create([
                        'question_id' => $qstion->id,
                        'answer' => $q['answers']['answer'][$i],
                        'is_answer' => $q['answers']['is_answer'][$i],
                    ]);
                }
            }
        }
        // return $request;
        // END OF create_test
        return $test;
    }

    public function delete_test(Request $request, $id) {
        $test = Test::findOrFail($id);
        $test->delete();

        return $test;
    }

    public function update_test(Request $request,$id) {
        $test = Test::findOrFail($id);
        $this->validate($request, [
            'time_start' => 'required',
            'time_end' => 'required'
        ]);
        $test->fill($request->all());
        $test->save();

        return $this->response->item($test, new TestTransformer);
    }
}
