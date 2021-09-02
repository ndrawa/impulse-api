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

            if (Room::where('name', $row[2])->first() == null) {
                $room = Room::create([
                    'name' => $row[2],
                    'desc' => '-',
                    'msteam_code' => 'belum ada',
                    'msteam_link' => 'belum ada',
                ]);
                $room->save();
            }

            if (Course::where('code', $row[3])->where('name', $row[4])->first() == null) {
                $course = Course::create([
                    'code' => $row[3],
                    'name' => $row[4],
                ]);
                $course->save();
            }

            if (AcademicYear::where('year', $row[9])->where('semester', replace_semester($row[8]))->first() == null) {
                $academic_year = AcademicYear::create([
                    'year'      => $row[9],
                    'semester'  => replace_semester($row[8]),
                ]);
                $academic_year->save();
            }

            if (Classroom::where('name', $row[5])->first() == null) {
                $classroom = Classroom::create([
                    'name' => $row[5],
                ]);
                $classroom->save();
            }

            $room = Room::where('name', $row[2])->first();
            $course = Course::where('code', $row[3])->first();
            $academic_year = AcademicYear::where('year', $row[9])
                            ->where('semester', replace_semester($row[8]))
                            ->first();
            $staff = Staff::where('code', $row[6])->first();
            $classroom = Classroom::where('name', $row[5])->first();

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
    
                        $time = explode(' - ',trim($row[1]));
                        //Generate Schedule 1-14
                        $schedule = Schedule::create([
                            'name' => 'Module '.$i,
                            'time_start' => null,
                            'time_end' => null,
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
