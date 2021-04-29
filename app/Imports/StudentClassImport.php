<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\StudentClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentClassImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) 
        {
            if($key < 1 ) continue;
            // create Student classes
            if (Student::where('nim', $row[0])->first() != null) 
            {
                $student_id = Student::where('nim', $row[0])->first();
                $class_id = Classroom::where('name', $row[2])->first();
                if (StudentClass::where(['student_id' => $student_id->id, 'class_id' => $class_id->id])->first() == null) 
                {
                    StudentClass::create([
                        'student_id' => $student_id->id,
                        'class_id' => $class_id->id,
                    ]);
                }
            }
        }
    }
}
