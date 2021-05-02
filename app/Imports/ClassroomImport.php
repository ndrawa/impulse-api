<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\Course;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClassroomImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) 
        {
            if($key < 1 ) continue;
            if (Student::where('nim', $row[0])->first() != null) 
            {
                if (Classroom::where('name', $row[2])->first() == null) 
                {
                    $staff_id = Staff::where('code', $row[10])->first();
                    $course_id = Course::where('code', $row[8])->first();
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
}
