<?php

namespace App\Exports;

use App\Models\Loadplan;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoadplanExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use Exportable;

    private $fileName = null;

    private $writerType = Excel::XLSX;

    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct()
    {
        $this->fileName = 'Loadplan_export_(' . now()->format('d-m-Y') . ')_' . time() . '.xlsx';
    }

    public function collection()
    {
        $collection = collect();

        foreach (Loadplan::all() as $value) {
            $collection->push([
                'line' => intval($value->line),
                'spk_publish' => $value->spk_publish ? Date::dateTimeToExcel(now()->parse($value->spk_publish)->toDateTime()) : null,
                'release' => Date::dateTimeToExcel(now()->parse($value->release)->toDateTime()),
                'doc_date' => Date::dateTimeToExcel(now()->parse($value->doc_date)->toDateTime()),
                'po_number' => intval($value->po_number),
                'style_number' => $value->style_number,
                'model_name' => $value->model_name,
                'invoice' => $value->invoice,
                'destination' => $value->destination,
                'ogac' => $value->ogac ? Date::dateTimeToExcel(now()->parse($value->ogac)->toDateTime()) : null,
                'qty_origin' => floatval($value->qty_origin),
                'special' => $value->special,
                'remark' => $value->remark,
            ]);
        }
        return $collection;
    }

    public function headings(): array
    {
        return [
            'Line',
            'SPK Publish',
            'Release',
            'Doc Date',
            'PO Number',
            'Style Number',
            'Model Name',
            'Invoice',
            'Destination',
            'OGac',
            'Qty Origin',
            'Special',
            'Remark'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '0',
            'B' => 'm/d',
            'C' => 'm/d',
            'D' => 'm/d',
            'E' => '0',
            'J' => 'm/d',
            'K' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getSheetView()->setZoomScale(80);
        $sheet->getDefaultRowDimension()->setRowHeight(0.21, 'in');

        $headerColumn = 'A1:M1';
        $AllColumn = 'A1:M' . $sheet->getHighestRow();

        $sheet->setAutoFilter($AllColumn);

        $getStyle = $sheet->getStyle($AllColumn);
        $getStyle->getFont()
            ->setName('Tahoma')
            ->setSize(11);
        $getStyle->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');
        $getStyle->getBorders()
            ->getAllBorders()
            ->setBorderStyle('dashed');

        $headerStyle = $sheet->getStyle($headerColumn);
        $headerStyle->getFont()
            ->setBold(true);
        $headerStyle->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('89fac5');
    }
}
