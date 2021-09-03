<?php

namespace App\Http\Controllers\Api\V1\Module;

use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\Request;

use App\Models\Schedule;
use App\Models\Course;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;
use App\Models\AcademicYear;
use App\Models\Module;

use App\Transformers\ModuleTransformer;

use Illuminate\Validation\Rule;

class ModuleController extends BaseController
{
    public function getCourseModule(Request $request, $course_id, $academic_year_id) {
        $course = Course::find($course_id);
        $academic_year = AcademicYear::find($academic_year_id);
        if(empty($course) or empty($academic_year)) {
            return $this->response->noContent();
        }
        //get the modules with specific course id and academic year id
        $modules = Module::where('course_id',$course->id)
                        ->where('academic_year_id', $academic_year->id)
                        ->get();
        if(empty($modules)) {
            return $this->response->noContent();
        }
        $arr = [];

        foreach($modules as $key=>$m) {
            $pretest = Test::find($m['pretest_id']);
            $posttest = Test::find($m['posttest_id']);
            $journal = Test::find($m['journal_id']);
            if(empty($pretest)) {
                $pretest = null;
            }
            if(empty($posttest)) {
                $posttest = null;
            }
            if(empty($journal)) {
                $journal = null;
            }

            $academic_year = AcademicYear::where('id', $m['academic_year_id'])->first();

            $arr[$key]['course']['id'] = $course['id'];
            $arr[$key]['course']['name'] = $course['name'];
            $arr[$key]['pretest'] = $pretest;
            $arr[$key]['posttest'] = $posttest;
            $arr[$key]['journal'] = $journal;
            $arr[$key]['index'] = $m['index'];
            $arr[$key]['academic_year']['id'] = $academic_year['id'];
            $arr[$key]['academic_year']['year'] = $academic_year['year'];
            $arr[$key]['academic_year']['semester'] = $academic_year['semester'];
        }

        $data['data'] = $arr;
        return $data;
        // return $this->response->collection($modules, new ModuleTransformer);
    }

    public function getModule(Request $request, $course_id, $academic_year_id, $index) {
        $course = Course::find($course_id);
        $academic_year = AcademicYear::find($academic_year_id);
        if(empty($course) or empty($academic_year)) {
            return $this->response->noContent();
        }
        //get the modules with specific course id, academic year id, and index
        $module = Module::where('index', $index)
                    ->where('course_id', $course->id)
                    ->where('academic_year_id', $academic_year->id)
                    ->first();
        if(empty($module)) {
            return $this->response->noContent();
        }

        $pretest = Test::find($module['pretest_id']);
        $posttest = Test::find($module['posttest_id']);
        $journal = Test::find($module['journal_id']);
        if(empty($pretest)) {
            $pretest = null;
        }
        if(empty($posttest)) {
            $posttest = null;
        }
        if(empty($journal)) {
            $journal = null;
        }
        $arr['course']['id'] = $course['id'];
        $arr['course']['name'] = $course['name'];
        $arr['pretest'] = $pretest;
        $arr['posttest'] = $posttest;
        $arr['journal'] = $journal;
        $arr['index'] = $module['index'];
        $arr['academic_year']['id'] = $academic_year['id'];
        $arr['academic_year']['year'] = $academic_year['year'];
        $arr['academic_year']['semester'] = $academic_year['semester'];

        $data['data'] = $arr;
        return $data;
    }

    public function show(Request $request, $id)
    {
        $module = Module::find($id);
        // $module = Module::where('id', $id)->first();
        if(empty($module)) {
            return $this->response->noContent();
        }

        $pretest = Test::find($module['pretest_id']);
        $posttest = Test::find($module['posttest_id']);
        $journal = Test::find($module['journal_id']);
        $course = Course::find($module['course_id']);
        $academic_year = AcademicYear::find($module['academic_year_id']);
        if(empty($pretest)) {
            $pretest = null;
        }
        if(empty($posttest)) {
            $posttest = null;
        }
        if(empty($journal)) {
            $journal = null;
        }
        $arr['course']['id'] = $course['id'];
        $arr['course']['name'] = $course['name'];
        $arr['pretest'] = $pretest;
        $arr['posttest'] = $posttest;
        $arr['journal'] = $journal;
        $arr['index'] = $module['index'];
        $arr['academic_year']['id'] = $academic_year['id'];
        $arr['academic_year']['year'] = $academic_year['year'];
        $arr['academic_year']['semester'] = $academic_year['semester'];

        $data['data'] = $arr;
        return $data;

        // return $this->response->item($module, new ModuleTransformer);
    }

    public function updateTest(Request $request, $id) {
        $this->validate($request, [
            'test_type' => 'required',
            'test_id' => 'required',
        ]);
        $module = Module::find($id);
        if(!$module) {
            return $this->response->errorNotFound('invalid module id');
        }
        $test = Test::find($request->test_id);
        if(!$test) {
            return $this->response->errorNotFound('invalid test id');
        }
        if($request->test_type == 'pretest') {
            $module->update(['pretest_id' => $request->test_id]);
        } else if($request->test_type == 'posttest') {
            $module->update(['posttest_id' => $request->test_id]);
        } else if($request->test_type == 'journal') {
            $module->update(['journal_id' => $request->test_id]);
        }
        $module->save();
    }
}
