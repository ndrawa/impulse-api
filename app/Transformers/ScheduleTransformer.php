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
            'class_course_id' => $schedule->class_course_id,
            'module_id' => $schedule->module_id,
            'academic_year_id' => $schedule->academic_year_id,
            'date' => $schedule->date
        ];
    }
}


