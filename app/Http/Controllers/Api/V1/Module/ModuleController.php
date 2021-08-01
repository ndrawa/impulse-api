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
        $course = Course::findOrFail($course_id);
        $academic_year = AcademicYear::findOrFail($academic_year_id);

        //get the modules with specific course id and academic year id
        $modules = Module::where('course_id',$course->id)
                        ->where('academic_year_id', $academic_year->id)
                        ->get();
        return $this->response->collection($modules, new ModuleTransformer);
    }

    public function getModule(Request $request, $course_id, $academic_year_id, $index) {
        $course = Course::findOrFail($course_id);
        $academic_year = AcademicYear::findOrFail($academic_year_id);
        //get the modules with specific course id, academic year id, and index
        $module = Module::where('index', $index)
                    ->where('course_id', $course->id)
                    ->where('academic_year_id', $academic_year->id)
                    ->firstOrFail();
        return $this->response->item($module, new ModuleTransformer);
    }

    public function show(Request $request, $id)
    {
        //$module = Module::findOrFail($id);
        $module = Module::where('id', $id)->first();
        return $this->response->item($module, new ModuleTransformer);
    }
}
