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
            'time_start' => $schedule->time_start,
            'time_end' => $schedule->time_end,
            'room' => $schedule->room,
            'class_course_id' => $schedule->class_course_id,
            'module' => $schedule->module,
            'academic_year_id' => $schedule->academic_year_id,
            'date' => $schedule->date,
            'schedule_test' => $schedule->schedule_test,
        ];
    }
}


