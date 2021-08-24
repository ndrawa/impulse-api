<?php

namespace App\Http\Controllers\Api\V1\Schedule;

use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleTest;
use App\Models\Module;
use App\Models\Test;
use App\Transformers\ScheduleTestTransformer;

class ScheduleTestController extends BaseController
{
    public function get(Request $request, $id)
    {
        $schedule_test = ScheduleTest::find($id);

        return $this->response->item($schedule_test, new ScheduleTestTransformer);
    }

    public function getScheduleTest(Request $request, $schedule_id, $test_id) {
        $schedule_tests = ScheduleTest::where('schedule_id', $schedule_id)
                                        ->where('test_id', $test_id)
                                        ->get();

        return $this->response->collection($schedule_tests, new ScheduleTestTransformer);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'schedule_id' => 'required',
            'test_id' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'auth' => 'required',
        ]);
        $request['is_active'] = false;

        if(!Schedule::find($request->schedule_id) or !Test::find($request->test_id)) {
            return $this->response->error('schedule id or test id not found', 404);
        }
        if(ScheduleTest::where('schedule_id', $request->schedule_id)->first() and
            ScheduleTest::where('test_id', $request->test_id)->first() ) {
            return $this->response->errorBadRequest('schedule test already exist');
        }

        $schedule_test = ScheduleTest::create($request->all());

        return $this->response->item($schedule_test, new ScheduleTestTransformer);
    }

    public function update(Request $request, $id) {
        $schedule_test = ScheduleTest::find($id);
        if(!$schedule_test) {
            return $this->response->error('schedule test not found', 404);
        }
        // $this->authorize('update', $classroom);
        $this->validate($request, [
            'schedule_id' => 'required',
            'test_id' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'is_active' => 'required',
            'auth' => 'required',
        ]);
        if(!Schedule::find($request->schedule_id) or !Test::find($request->test_id)) {
            return $this->response->error('schedule id or test id not found', 404);
        }
        $schedule_test->fill($request->all());
        $schedule_test->save();

        return $this->response->item($schedule_test, new ScheduleTestTransformer);
    }
}
