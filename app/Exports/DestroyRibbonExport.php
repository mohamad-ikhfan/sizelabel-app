<?php

namespace App\Exports;

use App\Models\DestroyRibbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DestroyRibbonExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use Exportable;

    private $fileName = null;

    private $writerType = Excel::XLSX;

    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public $from, $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $collection = collect();
        foreach (DestroyRibbon::where('date', '>=', $this->from)->where('date', '<=', $this->to)->orderBy('date')->get() as $destroyRibbon) {
            $collection->push([
                'date' => Date::dateTimeToExcel(now()->parse($destroyRibbon->date)->toDateTime()),
                'qty' => $destroyRibbon->qty,
                'pic' => $destroyRibbon->user->name,
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return [
            'DATE',
            'QTY',
            'PIC'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'dd-mmmm-yyyy',
            'B' => '0'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $headerColumn = 'A1:C1';
        $AllColumn = 'A1:C' . $sheet->getHighestRow();

        $getStyle = $sheet->getStyle($AllColumn);
        $getStyle->getFont()
            ->setName('Tahoma')
            ->setSize(11);
        $getStyle->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');
        $getStyle->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        $headerStyle = $sheet->getStyle($headerColumn);
        $headerStyle->getFont()
            ->setBold(true);
        $headerStyle->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('89fac5');
    }
}