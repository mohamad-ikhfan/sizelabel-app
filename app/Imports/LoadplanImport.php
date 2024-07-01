<?php

namespace App\Imports;

use App\Models\Loadplan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LoadplanImport implements ToArray, WithEvents
{
    use Importable;

    public $sheetName;

    public function array(array $array)
    {
        foreach (array_slice($array, 23) as $column) {
            if (!empty($column[5]) && !empty($column[12]) && !empty($column[34])) {
                $data = [
                    'line' => trim($this->sheetName),
                    'spk_publish' => is_numeric($column[7]) && $column[7] > 0 ? Date::excelToDateTimeObject($column[7])->format('Y-m-d') : null,
                    'release' => Date::excelToDateTimeObject($column[9])->format('Y-m-d'),
                    'doc_date' => is_numeric($column[10]) ? Date::excelToDateTimeObject($column[10])->format('Y-m-d') : null,
                    'po_number' => is_numeric($column[11]) ? $column[11] : null,
                    'style_number' => trim($column[12]),
                    'model_name' => trim($column[13]),
                    'invoice' => trim($column[14]),
                    'destination' => trim($column[15]),
                    'ogac' => is_numeric($column[16]) ? Date::excelToDateTimeObject($column[16])->format('Y-m-d') : null,
                    'qty_origin' => $column[34],
                    'special' => is_string($column[10]) ? trim($column[10]) : "-",
                    'remark' => !empty($column[4]) ? trim($column[4]) : "-",
                ];

                $loadplan = Loadplan::where([
                    'release' => is_numeric($column[16]) ? Date::excelToDateTimeObject($column[9])->format('Y-m-d') : null,
                    'po_number' => $column[11],
                    'qty_origin' => $column[34],
                ])->first();

                if ($loadplan) {
                    $loadplan->update($data);
                } else {
                    Loadplan::create($data);
                }
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetName =  $event->getSheet()->getTitle();
            }
        ];
    }
}