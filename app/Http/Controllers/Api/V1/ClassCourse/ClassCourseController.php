<?php

namespace App\Http\Controllers\Api\V1\ClassCourse;

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

class ClassCourseController extends BaseController
{
    public function index(Request $request) {

    }

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
                $class_course = ClassCourse::firstOrNew($request->all());
                $class_course->save();

                //Generate Module 1-14
                for ($i = 1; $i < 15; $i++) {
                    $module = '';
                    if (Module::where('course_id', $request->course_id)->where('index', $i)->first() == null) {
                        $module = Module::create([
                            'course_id' => $request->course_id,
                            'index' => $i,
                            'academic_year_id' => $request->academic_year_id,
                        ]);
                        $module->save();
                    } else {
                        $module = Module::where('course_id', $request->course_id)->where('index', $i)->first();
                    }

                    //Generate Schedule 1-14
                    $schedule = Schedule::create([
                        'name' => 'Module '.$i,
                        'time_start' => '2021-01-12 07:30:00',
                        'time_end' => '2021-01-12 10:30:00',
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
        $class_course = ClassCourse::all();

        $kelas = null;
        if($request->has('kelas')) {
            $kelas = strtoupper($request->get('kelas'));
        }
        $index = 0;
        $idx = 0;

        $arr = [];
        foreach($class_course as $key=>$cc) {
            $classroom = Classroom::select('name')->where('id', $cc['class_id'])->first();
            $staff = Staff::select('name','code')->where('id', $cc['staff_id'])->first();
            $course = Course::select('name','code')->where('id', $cc['course_id'])->first();
            $academic_year = AcademicYear::where('id', $cc['academic_year_id'])->first();

            $isTrue = false;
            if($kelas != null){
                $isTrue = str_contains($classroom->name, $kelas);
            }

            if ($kelas == null || $isTrue){
                if($isTrue){
                    $idx = $index;
                    $index++;
                }
                else{
                    $idx = $key;
                }
                $arr[$idx]['id'] = $cc['id'];
                $arr[$idx]['class']['id'] = $cc['class_id'];
                $arr[$idx]['class']['name'] = $classroom->name;
                $arr[$idx]['staff']['id'] = $cc['staff_id'];
                $arr[$idx]['staff']['name'] = $staff->name;
                $arr[$idx]['staff']['code'] = $staff->code;
                $arr[$idx]['course']['id'] = $cc['course_id'];
                $arr[$idx]['course']['name'] = $course->name;
                $arr[$idx]['course']['code'] = $course->code;
                $arr[$idx]['academic_year']['id'] = $cc['academic_year_id'];
                $arr[$idx]['academic_year']['name'] = $academic_year->year;
                $arr[$idx]['academic_year']['semester'] = $academic_year->semester;
            }
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
            'class_course_id' => 'required'
        ]);

        $asprak = Asprak::create($request->all());

        return $this->response->item($asprak, new AsprakTransformer);
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
}
