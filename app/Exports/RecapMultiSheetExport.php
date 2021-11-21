<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecapMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $recaps;
    protected $classes;

    public function __construct(array $recaps, array $classes)
    {
        $this->recaps = $recaps;
        $this->classes = $classes;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach($this->classes as $key=>$class_name) {
            $sheets[] = new RecapExport($this->recaps[$key], $class_name);
        }

        return $sheets;
    }
}
