<?php

namespace App\Http\Controllers\Api\V1\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Schedule;
use App\Transformers\ScheduleTransformer;
use Illuminate\Validation\Rule;


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

    // public function create(Request $request)
    // {
    //     $this->authorize('create', Schedule::class);
    //     $this->validate($request, [
    //         'name' => 'required',
    //         'day' => 'required',
    //         'time_start' => 'required',
    //         'time_end' => 'required',
    //         'room_id' => 'required',
    //         'periode_start' => 'required',
    //         'periode_end' => 'required',
    //         'class_id' => 'required',
    //     ]);
    //     $schedule = Schedule::create($schedule->all());

    //     return $this->response->item($schedule, new ScheduleTransformer);
    // }
}
