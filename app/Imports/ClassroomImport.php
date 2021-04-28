<?php

namespace App\Imports;

use App\Models\Classroom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClassroomImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) 
        {
            if (Classroom::where('name', $row[2])->first() == null) {
                $staff_id = DB::table('staffs')
                    ->where('code', $row[10])
                    ->first();
                $course_id = DB::table('courses')
                    ->where('code', $row[8])
                    ->first();
                $classroom = Classroom::create([
                    'staff_id' => $staff_id->id,
                    'name' => $row[2],
                    'course_id' => $course_id->id,
                    'academic_year' => $row[11],
                    'semester' => $row[12],
                ]);
                $classroom->save();
            }
        }
    }
}
