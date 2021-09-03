<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\Room;
use App\Models\Course;
use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Models\Module;
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
        function replace_semester($data)
        {
            if ($data == 'ODD') {
                return 'odd';
            } else {
                return 'even';
            }
        }

        foreach ($collection as $key => $row)
        {
            if($key < 1 ) continue;

            if (Room::where('name', $row[3])->first() == null) {
                $room = Room::create([
                    'name' => $row[3],
                    'desc' => '-',
                    'msteam_code' => 'belum ada',
                    'msteam_link' => 'belum ada',
                ]);
                $room->save();
            }

            if (Course::where('code', $row[4])->where('name', $row[5])->first() == null) {
                $course = Course::create([
                    'code' => $row[4],
                    'name' => $row[5],
                ]);
                $course->save();
            }

            if (AcademicYear::where('year', $row[10])->where('semester', replace_semester($row[9]))->first() == null) {
                $academic_year = AcademicYear::create([
                    'year'      => $row[10],
                    'semester'  => replace_semester($row[9]),
                ]);
                $academic_year->save();
            }

            if (Classroom::where('name', $row[6])->first() == null) {
                $classroom = Classroom::create([
                    'name' => $row[6],
                ]);
                $classroom->save();
            }

            $room = Room::where('name', $row[3])->first();
            $course = Course::where('code', $row[4])->first();
            $academic_year = AcademicYear::where('year', $row[10])
                            ->where('semester', replace_semester($row[9]))
                            ->first();
            $staff = Staff::where('code', $row[7])->first();
            $classroom = Classroom::where('name', $row[6])->first();

            if($classroom && $staff && $course && $academic_year && $room)  {
                $class_course_check = ClassCourse::where('class_id', $classroom->id)
                                                ->where('course_id', $course->id)
                                                ->where('staff_id', $staff->id)
                                                ->where('academic_year_id', $academic_year->id)
                                                ->exists();
                if(!$class_course_check) {
                    $class_course = ClassCourse::firstOrNew([
                        'class_id' => $classroom->id,
                        'course_id' => $course->id,
                        'staff_id' => $staff->id,
                        'academic_year_id' => $academic_year->id
                    ]);
                    $class_course->save();
    
                    //Generate Module 1-14
                    for ($i = 1; $i < 15; $i++) {
                        $module = '';
                        if (Module::where('course_id', $course->id)->where('index', $i)->first() == null) {
                            $module = Module::create([
                                'course_id' => $course->id,
                                'index' => $i,
                                'academic_year_id' => $academic_year->id,
                            ]);
                            $module->save();
                        } else {
                            $module = Module::where('course_id', $course->id)->where('index', $i)->first();
                        }
    
                        $time = explode(' - ',trim($row[2]));
                        //Generate Schedule 1-14
                        $schedule = Schedule::create([
                            'name' => 'Module '.$i,
                            'time_start' => date("Y-m-d ", $row[1]).$time[0],
                            'time_end' => date("Y-m-d ", $row[1]).$time[1],
                            'room_id' => $room->id,
                            'class_course_id' => ClassCourse::where('class_id', $classroom->id)
                                                ->where('course_id', $course->id)
                                                ->where('staff_id', $staff->id)
                                                ->where('academic_year_id', $academic_year->id)
                                                ->first()->id,
                            'module_id' => $module->id,
                            'academic_year_id' => $academic_year->id,
                            'date' => \Carbon\Carbon::now()->toDateTimeString(),
                        ]);
                        $schedule->save();
                    }
                } 
            }
        }
    }
}
