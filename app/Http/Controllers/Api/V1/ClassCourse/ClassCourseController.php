<?php

namespace App\Http\Controllers\Api\V1\ClassCourse;

use App\Exports\RecapMultiSheetExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Module;
use App\Models\Room;
use App\Models\ClassCourse;
use App\Models\Staff;
use App\Models\Schedule;
use App\Models\Asprak;
use App\Models\StudentPresence;
use App\Models\Praktikan;
use App\Transformers\AsprakTransformer;
use App\Transformers\ClassCourseTransformer;
use App\Models\StudentClassCourse;
use Maatwebsite\Excel\Facades\Excel;

class ClassCourseController extends BaseController
{
    public function create_class_course(Request $request) {
        $this->validate($request, [
            'class_id' => 'required',
            'staff_id' => 'required',
            'course_id' => 'required',
            'academic_year_id' => 'required',
        ]);

        $classroom = Classroom::find($request->class_id);
        $staff = Staff::find($request->staff_id);
        $course = Course::find($request->course_id);
        $academic_year = AcademicYear::find($request->academic_year_id);

        if($classroom && $staff && $course && $academic_year) {
            $class_course_check = ClassCourse::where('class_id', $request->class_id)
                                            ->where('staff_id', $request->staff_id)
                                            ->where('course_id', $request->course_id)
                                            ->where('academic_year_id', $request->academic_year_id)
                                            ->exists();

            if(!$class_course_check) {
                $class_course = ClassCourse::firstOrCreate($request->all());
                $class_course->save();

                //Generate Module 1-14
                for ($i = 1; $i < 15; $i++) {
                    $module = Module::create([
                        'course_id' => $request->course_id,
                        'index' => $i,
                        'academic_year_id' => $request->academic_year_id,
                    ]);

                    //Generate Schedule 1-14
                    $schedule = Schedule::create([
                        'name' => 'Module '.$i,
                        'time_start' => null,
                        'time_end' => null,
                        'room_id' => Room::first()->id,
                        'class_course_id' => ClassCourse::where('class_id', $request->class_id)
                                            ->where('course_id', $request->course_id)
                                            ->where('staff_id', $request->staff_id)
                                            ->where('academic_year_id', $request->academic_year_id)
                                            ->first()->id,
                        'module_id' => $module->id,
                        'academic_year_id' => $request->academic_year_id,
                        'date' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]);
                    $schedule->save();
                }

                return $class_course;
            } else {
                return $this->response->errorBadRequest('Course class already exist');
            }
        } else {
            $err = [];
            if(!$classroom) {array_push($err, 'Classroom invalid');}
            if(!$staff) {array_push($err, 'Staff invalid');}
            if(!$course) {array_push($err, 'Course invalid');}
            if(!$academic_year) {array_push($err, 'Academic year invalid');}
            return json_encode($err);
        }
    }

    public function get_class_course(Request $request) {
        $class_course = ClassCourse::query();

        if($request->has('student_id')) {
            if(!empty($request->student_id)) {
                $student_class_course = StudentClassCourse::where('student_id', $request->student_id)->get();
                $x = [];
                foreach($student_class_course as $key=>$scc) {
                    $x[$key] = $scc->class_course_id;
                }
                $class_course = $class_course->whereIn('id', $x);
            }
        }
        if($request->has('asprak_id')) {
            if(!empty($request->asprak_id)) {
                $student_class_course = Asprak::where('student_id', $request->asprak_id)->get();
                $x = [];
                foreach($student_class_course as $key=>$scc) {
                    $x[$key] = $scc->class_course_id;
                }
                $class_course = $class_course->whereIn('id', $x);
            }
        }
        if($request->has('class_name')) {
            if(!empty($request->class_name)) {
                $classroom = Classroom::where('name', 'like','%'.$request->class_name.'%')
                                ->first();
                $class_course = $class_course->where('class_id', $classroom->id);
            }
        }

        if($request->has('course_name')) {
            if(!empty($request->course_name)) {
                $course = Course::where('name', 'like','%'.$request->course_name.'%')
                            ->first();
                $class_course = $class_course->where('course_id', $course->id);
            }
        }

        if($request->has('academic_year_id')) {
            if(!empty($request->academic_year_id)) {
                $class_course = $class_course->where('academic_year_id', $request->academic_year_id);
            }
        }

        $class_course = $class_course->get();

        $arr = [];
        foreach($class_course as $key=>$cc) {
            $classroom = Classroom::select('name')->where('id', $cc['class_id'])->first();
            $staff = Staff::select('name','code')->where('id', $cc['staff_id'])->first();
            $course = Course::select('name','code')->where('id', $cc['course_id'])->first();
            $academic_year = AcademicYear::where('id', $cc['academic_year_id'])->first();

            $arr[$key]['id'] = $cc['id'];
            $arr[$key]['class']['id'] = $cc['class_id'];
            $arr[$key]['class']['name'] = $classroom->name;
            $arr[$key]['staff']['id'] = $cc['staff_id'];
            $arr[$key]['staff']['name'] = $staff->name;
            $arr[$key]['staff']['code'] = $staff->code;
            $arr[$key]['course']['id'] = $cc['course_id'];
            $arr[$key]['course']['name'] = $course->name;
            $arr[$key]['course']['code'] = $course->code;
            $arr[$key]['academic_year']['id'] = $cc['academic_year_id'];
            $arr[$key]['academic_year']['name'] = $academic_year->year;
            $arr[$key]['academic_year']['semester'] = $academic_year->semester;
        }

        $data['data'] = $arr;

        return $data;
    }

    public function get_class_course_by_id(Request $request, $class_course_id) {
        $cc = ClassCourse::findOrFail($class_course_id);

        $classroom = Classroom::select('name')->where('id', $cc['class_id'])->first();
        $staff = Staff::select('name','code')->where('id', $cc['staff_id'])->first();
        $course = Course::select('name','code')->where('id', $cc['course_id'])->first();
        $academic_year = AcademicYear::where('id', $cc['academic_year_id'])->first();
        $asprakcc = Asprak::select('student_id','asprak_code')->where('class_course_id', $class_course_id)->get();

        $arr['id'] = $cc['id'];
        $arr['class']['id'] = $cc['class_id'];
        $arr['class']['name'] = $classroom->name;
        $arr['staff']['id'] = $cc['staff_id'];
        $arr['staff']['name'] = $staff->name;
        $arr['staff']['code'] = $staff->code;
        $arr['course']['id'] = $cc['course_id'];
        $arr['course']['name'] = $course->name;
        $arr['course']['code'] = $course->code;
        $arr['academic_year']['id'] = $cc['academic_year_id'];
        $arr['academic_year']['name'] = $academic_year->year;
        $arr['academic_year']['semester'] = $academic_year->semester;
        $arr['asprak_class_course'] = $asprakcc;

        $data['data'] = $arr;

        return $data;
    }

    public function get_class_course_staff_year(Request $request) {
        $data['data']['classes'] = Classroom::select('id','name')->get();
        $data['data']['courses'] = Course::select('id','code','name')->get();
        $data['data']['staffs'] = Staff::select('id','code','name')->get();
        $data['data']['academic_year'] = AcademicYear::select('id','year','semester')->get();

        return $data;
    }

    public function delete_class_course_by_id(Request $request, $class_course_id) {
        // Hapus menggunakan id:
        // {{url}}/v1/laboran/class-course/{class_course_id}
        // Hapus semua:
        // {{url}}/v1/laboran/class-course/all
        if($class_course_id != 'all') {
            $class_course = ClassCourse::find($class_course_id);

            if(!$class_course) {
                return $this->response->errorNotFound('invalid id');
            }

            $class_course->delete();
        }
        if($class_course_id == 'all') {
            $class_course = ClassCourse::truncate();
        }

        return $this->response->noContent();
    }

    public function set_asprak_class_course(Request $request) {
        $this->validate($request, [
            'student_id' => 'required',
            'asprak_code' => 'required',
            'class_course_id' => 'required'
        ]);
        if (!Asprak::where('student_id', $request->student_id)->where('class_course_id', $request->class_course_id)->first()) {
            $asprak = Asprak::create($request->all());
            return $this->response->item($asprak, new AsprakTransformer);
        } else {
            return $this->response->errorNotFound('Duplicate data.');
        }
    }

    public function get_asprak_class_course(Request $request) {
        $asprak = Asprak::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$asprak) {
            $asprak = $asprak->student->where('name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('nim', 'ILIKE', '%'.$search.'%')
                            ->orWhere('gender', 'ILIKE', '%'.$search.'%')
                            ->orWhere('religion', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $asprak->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $asprak->orderBy($orderBy);
        }

        $asprak = $asprak->paginate($per_page);

        return $this->response->paginator($asprak, new AsprakTransformer);
    }

    public function get_asprak_class_course_by_id(Request $request, $id) {
        $asprak = Asprak::where('id', $id)->first();
        return $this->response->item($asprak, new AsprakTransformer);
    }

    public function delete_asprak_class_course(Request $request, $id) {
        $asprak = Asprak::findOrFail($id);
        $asprak->delete();
        return $this->response->noContent();
    }

    public function showRecapPresence($class_course_id) {
        $class_course = ClassCourse::with(['classes', 'courses', 'schedule', 'student.student', 'schedule.module', 'academic_years', 'schedule.student_presence', 'schedule.bap'])
                        ->find($class_course_id);
        if(!$class_course) {
            return $this->response->errorNotFound('invalid class course id');
        }

        // $result = $this->simplifyRecapPresence($class_course);
        $result = $this->simplifyRecapPresence($class_course);
        return json_encode($result);
    }


    public function simpTest($class_course) {
        // return $class_course;
        $result = array(
            'id' => $class_course->id,
            'class' => array(
                'id' => $class_course->classes->id,
                'name' => $class_course->classes->name,
            ),
            'course' => array(
                'id' => $class_course->courses->id,
                'code'  => $class_course->courses->code,
                'name' => $class_course->courses->name,
            ),
            'academic_year' => array(
                'id' => $class_course->academic_years->id,
                'year' => $class_course->academic_years->year,
                'semester' => $class_course->academic_years->semester,
            )
        );

        $schedule = $this->sort_module($class_course->schedule); #array($class_course->schedule);

        $result = $schedule;
        return $schedule;
    }

    public function sort_module($schedule) {
        $size = count($schedule);
        $temp;
        for($i=0; $i<$size; $i++) {
            for($j=0; $j<$size-$i-1; $j++) {
                if($schedule[$j]['module']['index'] > $schedule[$j+1]['module']['index']) {
                    $temp = $schedule[$j];
                    $schedule[$j] = $schedule[$j+1];
                    $schedule[$j+1] = $temp;
                }
            }
        }

        return $schedule;
    }

    public function simplifyRecapPresence($class_course) {
        $data = array(
            'id' => $class_course->id,
            'class' => array(
                'id' => $class_course->classes->id,
                'name' => $class_course->classes->name,
            ),
            'course' => array(
                'id' => $class_course->courses->id,
                'code'  => $class_course->courses->code,
                'name' => $class_course->courses->name,
            ),
            'academic_year' => array(
                'id' => $class_course->academic_years->id,
                'year' => $class_course->academic_years->year,
                'semester' => $class_course->academic_years->semester,
            )
        );

        $schd = [];
        foreach($class_course->student as $key=>$student) {
            $grade = json_decode(app('App\Http\Controllers\Api\V1\GradeController')
                    ->getStudentGrades($student->student->id, $class_course->courses->id));

            $schd = $this->sort_module($class_course->schedule); #array($class_course->schedule);

            $data['students'][$key] = array(
                'id' => $student->student->id,
                'nim' => $student->student->nim,
                'name' => $student->student->name,
                'grade' => $grade->data->result[0]->modules,
            );

            foreach($grade->data->result[0]->modules as $g) {
                $student_presence_ids = array();

                if(count($class_course->schedule[$g->index-1]['student_presence']) > 0) {
                    foreach($class_course->schedule[$g->index-1]['student_presence'] as $std) {
                        array_push($student_presence_ids, $std->student_id);
                    }
                }

                if(in_array($student->student->id, $student_presence_ids)) {
                    $presence = true;
                } else {
                    $presence = false;
                }

               $g->presence = $presence;
            }
        }

        if(!empty($schd)){
            foreach($schd as $key=>$schedule) {
                $data['schedule'][$key] = array(
                    'id' => $schedule->id,
                    'name' => $schedule->name,
                    'module' => array(
                        'index' => $schedule->module->index,
                    ),
                    'student_presence' => $schedule->student_presence,
                );
                if(is_null($schedule->bap)) {
                    $data['schedule'][$key]['materi'] = null;
                    $data['schedule'][$key]['evaluasi'] = null;
                } else {
                    $data['schedule'][$key]['materi'] = $schedule->bap->materi;
                    $data['schedule'][$key]['evaluasi'] = $schedule->bap->evaluasi;
                }
            }
        }
        if(!empty($data['schedule'])){
            usort($data['schedule'], function ($item1, $item2) {
                return $item1['module'] <=> $item2['module'];
            });
        }

        return $data;
    }

    public function export_recap(Request $request, $course_id) {
        $class_courses = ClassCourse::select('class_course.id as class_course_id', 'courses.name as course', 'classes.name as class', 'staffs.code as staff', 'academic_years.year as year', 'academic_years.semester as semester')
                                    ->where('class_course.course_id', $course_id)
                                    ->join('courses','courses.id', '=', 'class_course.course_id')
                                    ->join('classes','classes.id', '=', 'class_course.class_id')
                                    ->join('staffs','staffs.id', '=', 'class_course.staff_id')
                                    ->join('academic_years','academic_years.id', '=', 'class_course.academic_year_id')
                                    ->get()
                                    ->toArray();

        if(empty($class_courses)){
            $data= [[["kelas mk masih kosong!"]]];
            $classes = ['Sheet1'];
            $file_name = "recap".".xlsx";
            return (new RecapMultiSheetExport($data, $classes))->download($file_name);
        }
        else{
            $headers[0] = ['', '', ''];
            $headers[1] = ['No.', 'NIM', 'Nama'];

            for ($index = 1; $index <= 14; $index++) {
                for ($x = 0; $x <= 7; $x++) {
                    switch ($x) {
                        case 0:
                            array_push($headers[0], $index);
                            array_push($headers[1], "KEHADIRAN");
                            break;
                        case 1:
                            array_push($headers[0], $index);
                            array_push($headers[1], "ASPRAK");
                            break;
                        case 2:
                            array_push($headers[0], $index);
                            array_push($headers[1], "EVALUASI");
                            break;
                        case 3:
                            array_push($headers[0], $index);
                            array_push($headers[1], "MATERI");
                            break;
                        case 4:
                            array_push($headers[0], $index);
                            array_push($headers[1], "T. AWAL");
                            break;
                        case 5:
                            array_push($headers[0], $index);
                            array_push($headers[1], "JURNAL");
                            break;
                        case 6:
                            array_push($headers[0], $index);
                            array_push($headers[1], "T. AKHIR");
                            break;
                        case 7:
                            array_push($headers[0], $index);
                            array_push($headers[1], "TOTAL");
                            break;
                    }
                }
            }

            $classes = [];
            $data = [];
            $file_name = "recap".".xlsx";

            foreach($class_courses as $key=>$class_course){
                array_push($classes, $class_course['class']);

                $recap = json_decode($this->showRecapPresence($class_course['class_course_id']));
                $file_name = $recap->course->name.".xlsx";

                if(!empty($recap->students)){
                    foreach($recap->students as $s_key=>$student){
                        for ($temp = 0; $temp <= 3; $temp++){
                            switch ($temp) {
                                case 0:
                                    $data[$key][$s_key] = [$s_key+1];
                                    break;
                                case 1:
                                    array_push($data[$key][$s_key], $student->nim);
                                    break;
                                case 2:
                                    array_push($data[$key][$s_key], $student->name);
                                    break;
                                case 3:
                                    foreach($student->grade as $g_key=>$grade) {
                                        for ($x = 0; $x <= 7; $x++) {
                                            switch ($x) {
                                                case 0:
                                                    $grade->presence ? array_push($data[$key][$s_key], "Hadir") : array_push($data[$key][$s_key], "Tidak Hadir");
                                                    break;
                                                case 1:
                                                    array_push($data[$key][$s_key], "");
                                                    break;
                                                case 2:
                                                    $s_key == 0 ? array_push($data[$key][$s_key], $recap->schedule[$g_key]->evaluasi) : array_push($data[$key][$s_key], "");
                                                    break;
                                                case 3:
                                                    $s_key == 0 ? array_push($data[$key][$s_key], $recap->schedule[$g_key]->materi) : array_push($data[$key][$s_key], "");
                                                    break;
                                                case 4:
                                                    array_push($data[$key][$s_key], $grade->pretest->grade);
                                                    break;
                                                case 5:
                                                    array_push($data[$key][$s_key], $grade->journal->grade);
                                                    break;
                                                case 6:
                                                    array_push($data[$key][$s_key], $grade->posttest->grade);
                                                    break;
                                                case 7:
                                                    array_push($data[$key][$s_key], $grade->total_grade);
                                                    break;
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
                else{
                    $data[$key] = [];
                }
                $data[$key] = array_merge($headers, $data[$key]);
            }
            return (new RecapMultiSheetExport($data, $classes))->download($file_name);
        }
    }

    public function select_asprak(Request $request) {
        $this->validate($request, [
            'asprak_class_course_id' => 'required',
            'student_id' => 'required',
        ]);
        $praktikan = Praktikan::firstOrNew($request->all());
        return $praktikan;
    }

    public function get_plotting_asprak() {
        $praktikan = Praktikan::get();
        return $praktikan;
    }

    public function edit_plotting_asprak(Request $request, $id) {
        $this->validate($request, [
            'asprak_class_course_id' => 'required',
            'student_id' => 'required',
        ]);
        $praktikan = Praktikan::findOrFail($id);
        $praktikan->fill($request->all());
        $praktikan->save();
        return $praktikan;
    }
}
