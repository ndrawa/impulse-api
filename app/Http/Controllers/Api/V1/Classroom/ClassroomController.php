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

        $request->whenHas('search', function($search) use (&$staffs) {
            $classrooms = $classrooms->where('name', 'LIKE', '%'.$search.'%');
        });

        $classrooms = $classrooms->paginate($per_page);

        return $this->response->paginator($classrooms, new ClassroomTransformer);
    }

    // without paginator
    // public function rooms(Request $request)
    // {
    //     $rooms = Room::get();

    //     return $this->response->collection($rooms, new RoomTransformer);
    // }
}
