<?php

namespace App\Imports;

use App\Models\Schedule;
use App\Models\Room;
use App\Models\Course;
use App\Models\Classroom;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ScheduleImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        foreach ($collection as $key => $row)
        {
            if($key < 1 ) continue;
            if(strcmp($row[0], 'SENIN')) {
                $day = 'monday';
            } else if(strcmp($row[0], 'SELASA')) {
                $day = 'tuesday';
            } else if(strcmp($row[0], 'RABU')) {
                $day = 'wednesday';
            } else if(strcmp($row[0], 'KAMIS')) {
                $day = 'thrusday';
            } else if(strcmp($row[0], 'JUMAT')) {
                $day = 'friday';
            } else if(strcmp($row[0], 'SABTU')) {
                $day = 'saturday';
            }

            $room = Room::where('name', $row[2])->first();
            if ($room == null) {
                $room = Room::create([
                    'name' => $row[2],
                ]);
            }

            $course = Course::where('code', $row[3])
                            ->where('name', $row[4])
                            ->first();
            if ($course == null) {
                $course = Course::create([
                    'code' => $row[3],
                    'name' => $row[4],
                ]);
            }

            $staff = Staff::where('code', $row[6])
                            ->first();
            $course = Course::where('code', $row[3])
                            ->first();
            $classroom = Classroom::where('name', $row[5])
                            ->where('course_id', $course->id)
                            ->where('staff_id', $staff->id)
                            ->first();
            if ($classroom == null) {
                $classroom = Classroom::create([
                    'staff_id' => $staff->id,
                    'name' => $row[5],
                    'course_id' => $course->id,
                    'academic_year' => '',
                    'semester' => '',
                ]);
            }

            $time = explode(' - ',trim($row[1]));
            Schedule::create([
                'name' => 'PRAKTIKUM '.$row[4],
                'day' => $day,
                'time_start' => $time[0],
                'time_end' => $time[1],
                'room_id' => $room->id,
                'periode_start' => null,
                'periode_end' => null,
                'class_id' => $classroom->id,
                'type' => $row[7],
                'module_id' => 'Belum ada',
            ]);
        }
    }
}
