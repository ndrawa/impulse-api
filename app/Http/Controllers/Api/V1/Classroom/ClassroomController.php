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
        $classrooms = Classroom::query()->get();
        // $per_page = env('PAGINATION_SIZE', 15);
        // $request->whenHas('per_page', function($size) use (&$per_page) {
        //     $per_page = $size;
        // });

        // $request->whenHas('search', function($search) use (&$staffs) {
        //     $classrooms = $classrooms->where('name', 'LIKE', '%'.$search.'%');
        // });

        // $classrooms = $classrooms->paginate($per_page);

        return $this->response->item($classrooms, new ClassroomTransformer);
    }

    public function show(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        return $this->response->item($classroom, new ClassroomTransformer);
    }

    public function create(Request $request)
    {
        // $this->authorize('create', Classroom::class);
        $this->validate($request, [
            'staff_id' => 'required',
            'name' => 'required',
            'course_id' => 'required',
            'academic_year' => 'required',
            'semester' => 'required'
        ]);
        $classroom = Classroom::create($request->all());

        return $this->response->item($classroom, new ClassroomTransformer);
    }

    public function update(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        // $this->authorize('update', $classroom);
        $this->validate($request, [
            'staff_id' => 'required',
            'name' => 'required',
            'course_id' => 'required',
            'academic_year' => 'required',
            'semester' => 'required'
        ]);
        $classroom->fill($request->all());
        $classroom->save();

        return $this->response->item($classroom, new ClassroomTransformer);
    }

    public function delete(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        // $this->authorize('delete', $classroom);
        $classroom->delete();

        return $this->response->noContent();
    }
    // without paginator
    // public function rooms(Request $request)
    // {
    //     $rooms = Room::get();

    //     return $this->response->collection($rooms, new RoomTransformer);
    // }
}
