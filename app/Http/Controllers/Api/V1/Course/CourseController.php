<?php

namespace App\Http\Controllers\Api\V1\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Course;
use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Transformers\CourseTransformer;
use Illuminate\Validation\Rule;

class CourseController extends BaseController
{
    public function index(Request $request)
    {
        $courses = Course::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$courses) {
            $courses = $courses->where('name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('code', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $courses->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $courses->orderBy($orderBy);
        }

        $courses = $courses->paginate($per_page);

        return $this->response->paginator($courses, new CourseTransformer);
    }

    public function getall(Request $request)
    {
        $courses = Course::query();

        if ($request->has('search')) {
            $request->whenHas('search', function($search) use (&$courses) {
                $courses = $courses->where('id', 'LIKE', '%'.$search.'%')->get();
            });
        } else {
            $courses = Course::get();
        }

        return $this->response->item($courses, new CourseTransformer);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'code' => [
                'required',
                Rule::unique('courses')
            ],
            'name' => 'required',
        ]);
        if(Course::where('code', $request->code)->first() == null){
            $course = Course::create($request->all());
            return $this->response->item($course, new CourseTransformer);
        } else {
            return $this->response->noContent();
        }
        // return $this->response->item($course, new CourseTransformer);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $this->validate($request, [
            'code' => [
                'required',
                Rule::unique('courses')->ignore($course->code, 'code')
            ],
            'name' => 'required',
        ]);
        $course->fill($request->all());
        $course->save();

        return $this->response->item($course, new CourseTransformer);
    }

    public function delete(Request $request, $id)
    {
        if (ClassCourse::where('course_id', $id)->first() == null) {
            $course = Course::findOrFail($id);
            $course->delete();
            return $this->response->noContent();
        } else {
            return $this->response->error('This course is in use.', 500);
        }
    }
}
