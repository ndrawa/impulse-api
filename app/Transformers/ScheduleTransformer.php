<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Schedule;

class ScheduleTransformer extends TransformerAbstract
{
    public function transform(Schedule $schedule)
    {
        return [
            'id' => $schedule->id,
            'name' => $schedule->name,
            'day' => $schedule->day,
            'time_start' => $schedule->time_start,
            'time_end' => $schedule->time_end,
            'room_id' => $schedule->room_id,
            'periode_start' => $schedule->periode_start,
            'periode_end' => $schedule->periode_end,
            'class_course_id' => $schedule->class_course_id,
            'class_id' => $schedule->class_id,
            'module_id' => $schedule->module_id,
            'type' => $schedule->type
        ];
    }
}


