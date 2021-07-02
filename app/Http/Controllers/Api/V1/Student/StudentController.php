<?php

namespace App\Http\Controllers\Api\V1\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Student;
use App\Transformers\StudentTransformer;
use Illuminate\Validation\Rule;


class StudentController extends BaseController
{
    public function index(Request $request)
    {
        $students = Student::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$students) {
            $students = $students->where('name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('nim', 'ILIKE', '%'.$search.'%')
                            ->orWhere('gender', 'ILIKE', '%'.$search.'%')
                            ->orWhere('religion', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $students->orderBy($orderBy, $sortedBy);
        } 
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $students->orderBy($orderBy);
        }

        $students = $students->paginate($per_page);

        return $this->response->paginator($students, new StudentTransformer);
    }

    public function show(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        return $this->response->item($student, new StudentTransformer);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Student::class);
        $this->validate($request, [
            'name' => 'required',
            'nim' => [
                'required',
                Rule::unique('students')
            ],
            'gender' => [
                'required'
            ],
            'religion' => [
                'required'
            ]
        ]);
        $student = Student::create($request->all());

        return $this->response->item($student, new StudentTransformer);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('update', $student);
        $this->validate($request, [
            'name' => 'required',
            'nim' => [
                'required',
                Rule::unique('students')->ignore($student->nim, 'nim')
            ],
            'gender' => [
                'required'
            ],
            'religion' => [
                'required'
            ]
        ]);
        $student->fill($request->all());
        $student->save();

        return $this->response->item($student, new StudentTransformer);
    }

    public function delete(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('delete', $student);
        $student->delete();

        return $this->response->noContent();
    }
}
