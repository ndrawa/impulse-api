<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class RecapExport implements FromArray, WithTitle, WithStrictNullComparison, WithEvents
{
    protected $recaps;
    protected $class_name;

    public function __construct(array $recaps, string $class_name)
    {
        $this->recaps = $recaps;
        $this->class_name = $class_name;
    }

    public function array(): array
    {
        return $this->recaps;
    }

    public function title(): string
    {
        return $this->class_name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:DK1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('A2:DK2')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }
        ];
    }
}
