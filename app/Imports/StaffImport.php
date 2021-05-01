<?php

namespace App\Imports;

use App\Models\Staff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StaffImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) 
        {
            if($key < 1 ) continue;
            if ((Staff::where('nip', $row[0])->first() == null) && (Staff::where('code', $row[2])->first() == null)) 
            {
                Staff::create([
                    'name' => $row[1],
                    'nip' => $row[0],
                    'code' => $row[2]
                ]);
            }
        }
    }
}

