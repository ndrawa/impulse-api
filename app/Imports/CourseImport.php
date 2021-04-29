<?php

namespace App\Imports;

use App\Models\Course;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CourseImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) 
        {
            if($key < 1 ) continue;
            if (Course::where('code', $row[8])->first() == null) {
                $course = Course::create([
                    'name' => $row[9],
                    'code' => $row[8]
                ]);
            }
        }
    }
}
