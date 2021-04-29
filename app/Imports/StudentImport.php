<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        function replace_gender($data)
        {
            if ($data == 'PRIA') {
                return 'male';
            } else {
                return 'female';
            }
        }

        function replace_religion($data)
        {
            if ($data == 'ISLAM') {
                return 'islam';
            } elseif ($data == 'PROTESTAN') {
                return 'protestan';
            } elseif ($data == 'KATOLIK') {
                return 'katolik';
            } elseif ($data == 'BUDDHA') {
                return 'buddha';
            } elseif ($data == 'HINDU') {
                return 'hindu';
            } elseif ($data == 'KHONGHUCU') {
                return 'khonghucu';
            } elseif ($data == 'KRISTEN') {
                return 'kristen';
            }
        }

        foreach ($collection as $key => $row) 
        {
            if($key < 1 ) continue;
            Student::create([
                'name' => $row[1],
                'nim' => $row[0],
                'gender' => replace_gender($row[4]),
                'religion' => replace_religion($row[5]),
            ]);
        }
    }
}
