<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;

use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;
use App\Models\StudentClassCourse;
use App\Models\ClassCourse;
use App\Transformers\ScheduleTransformer;
use App\Transformers\TestTransformer;
use App\Transformers\ClassCourseTransformer;
use Illuminate\Validation\Rule;
use App\Imports\ScheduleImport;
use Maatwebsite\Excel\Facades\Excel;

class AcademicYearController extends BaseController
{
    public function index(Request $request)
    {
        $academicYear = AcademicYear::get();

        // return $this->response->paginator($academicYears, new AcademicYearTransformer);
        return $academicYear;
    }

    public function show(Request $request, $id){
        $academicYear = AcademicYear::Where('id', $id)->get();

        // return $this->response->item($academicYear, new AcademicYearTransformer);
        return $academicYear;
    }

    public function update(Request $request, $id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        // $this->authorize('update', $schedule);
        $this->validate($request, [
            'year' => 'required',
            'semester' => [
                'required'
            ]
        ]);
        $academicYear->fill($request->all());
        $academicYear->save();

        // return $this->response->item($academicYear, new AcademicYearTransformer);
        return $academicYear;
    }
    
    public function delete(Request $request, $id) {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->delete();

        return $this->response->noContent();
    }

    public function create(Request $request)
    {
        // $this->authorize('create', AcademicYear::class);
        $this->validate($request, [
            'year' => 'required',
            'semester' => [
                'required'
            ]
        ]);
        $academicYear = AcademicYear::create($request->all());

        // return $this->response->item($academicYear, new AcademicYearTransformer);
        return $academicYear;
    }
}
