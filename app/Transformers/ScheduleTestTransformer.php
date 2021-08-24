<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ScheduleTest;

class ScheduleTestTransformer extends TransformerAbstract
{
    public function transform(ScheduleTest $schedule_test)
    {
        return [
            'id' => $schedule_test->id,
            'schedule' => $schedule_test->schedule,
            'test' => $schedule_test->test,
            'time_start' => $schedule_test->time_start,
            'time_end' => $schedule_test->time_end,
            'is_active' => $schedule_test->is_active,
            'auth' => $schedule_test->auth,
        ];
    }
}


