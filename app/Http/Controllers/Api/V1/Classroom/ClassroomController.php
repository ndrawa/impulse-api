<?php

namespace App\Http\Controllers\Api\V1\Classroom;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Classroom;
use App\Transformers\ClassroomTransformer;
use Illuminate\Validation\Rule;

class ClassroomController extends BaseController
{
    public function index(Request $request)
    {
        $classrooms = Classroom::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$classrooms) {
            $classrooms = $classrooms->where('name', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $classrooms->orderBy($orderBy, $sortedBy);
        }
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $classrooms->orderBy($orderBy);
        }

        $classrooms = $classrooms->paginate($per_page);

        return $this->response->paginator($classrooms, new ClassroomTransformer);
    }

    public function getall(Request $request)
    {
        $classrooms = Classroom::query();

        if ($request->has('search')) {
            $request->whenHas('search', function($search) use (&$classrooms) {
                $classrooms = $classrooms->where('name', 'ILIKE', '%'.$search.'%')->get();
            });
        } else {
            $classrooms = Classroom::get();
        }

        return $this->response->item($classrooms, new ClassroomTransformer);
    }

    public function byname(Request $request)
    {
        $classroom = Classroom::select('name')->orderBy('name')->get();
        return $this->response->item($classroom, new ClassroomTransformer);
    }

    public function show(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        // $classroom = Classroom::where($id)->get();
        return $this->response->item($classroom, new ClassroomTransformer);
    }

    public function create(Request $request)
    {
        // $this->authorize('create', Classroom::class);
        $this->validate($request, [
            'name' => 'required'
        ]);
        if(Classroom::where('name', $request->name)->first() == null){
            $classroom = Classroom::create($request->all());
            return $this->response->item($classroom, new ClassroomTransformer);
        } else {
            return $this->response->error('Data already exist', 422);
        }
    }

    public function update(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        // $this->authorize('update', $classroom);
        $this->validate($request, [
            'name' => 'required'
        ]);
        $classroom->fill($request->all());
        $classroom->save();

        return $this->response->item($classroom, new ClassroomTransformer);
    }

    public function delete(Request $request, $id)
    {
        if (ClassCourse::where('class_id', $id)->first() == null) {
            $classroom = Classroom::findOrFail($id);
            $classroom->delete();
            return $this->response->noContent();
        } else {
            return $this->response->error('This classroom is in use.', 500);
        }
    }
}
