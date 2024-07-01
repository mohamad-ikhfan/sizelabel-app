<?php

namespace App\Imports;

use App\Models\ReportPrint;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ReportPrintImport implements ToArray
{
    use Importable;

    public function array(array $array)
    {
        foreach (array_slice($array, 1) as $column) {
            if (!empty($column[0]) && !empty($column[2]) && !empty($column[4]) && !empty($column[8])) {
                $data = [
                    'print_date' => Date::excelToDateTimeObject($column[0])->format('Y-m-d'),
                    'line' => $column[1],
                    'po_number' => $column[2],
                    'release' => Date::excelToDateTimeObject($column[3])->format('Y-m-d'),
                    'style_number' => trim($column[4]),
                    'model_name' => trim($column[5]),
                    'special' => trim($column[6]),
                    'qty_total' => $column[8],
                    'remark' => trim($column[9]),
                    'user_id' => $column[10],
                ];

                $reportPrint = ReportPrint::where([
                    'release' => is_numeric($column[3]) ? Date::excelToDateTimeObject($column[3])->format('Y-m-d') : null,
                    'po_number' => $column[2],
                    'qty_total' => $column[8],
                ])->first();

                if ($reportPrint) {
                    $reportPrint->update($data);
                } else {
                    ReportPrint::create($data);
                }
            }
        }
    }
}