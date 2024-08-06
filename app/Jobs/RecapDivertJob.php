<?php

namespace App\Jobs;

use App\Models\Loadplan;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class RecapDivertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $receipent, $file, $fileName;
    /**
     * Create a new job instance.
     */
    public function __construct(User $receipent, $file, $fileName)
    {
        $this->receipent = $receipent;
        $this->file = $file;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($this->file);

            $rekapDivert = $spreadsheet->getSheet(0);
            $rowDiverts = $rekapDivert->toArray();
            foreach ($rowDiverts as $cellDivert) {
                if (intval($cellDivert[0]) > 0) {
                    $oldPoItemDivert = $cellDivert[9];
                    $divertLoadplan = Loadplan::where('po_number', $oldPoItemDivert)->first();
                    if ($divertLoadplan) {
                        $new_po_items = collect();
                        foreach (explode(',', $cellDivert[15]) as $remark) {
                            $strpos = strpos($remark, "diverted to");
                            if ($strpos) {
                                $new_po_items->push(([
                                    'line' => $divertLoadplan->line,
                                    'spk_publish' => $divertLoadplan->spk_publish,
                                    'release' => $divertLoadplan->release,
                                    'doc_date' => is_numeric($cellDivert[2]) ? Date::excelToDateTimeObject($cellDivert[2])->format('Y-m-d') : null,
                                    'po_number' => intval(str_replace('-', '', substr($remark, $strpos + 12))),
                                    'style_number' => $divertLoadplan->style_number,
                                    'model_name' => $divertLoadplan->model_name,
                                    'invoice' => null,
                                    'destination' => $cellDivert[6],
                                    'ogac' => is_numeric($cellDivert[10]) ? Date::excelToDateTimeObject($cellDivert[10])->format('Y-m-d') : null,
                                    'qty_origin' => floatval(substr($remark, 0, ($strpos - 4))),
                                    'special' => '-',
                                    'remark' => $divertLoadplan->remark,
                                ]));
                            }
                        }

                        foreach ($new_po_items->toArray() as $new_po_item) {
                            Loadplan::create($new_po_item);
                        }

                        if (intval($cellDivert[13]) == 0) {
                            $divertLoadplan->delete();
                        } else {
                            $divertLoadplan->update(['qty_origin' => floatval($cellDivert[13])]);
                        }
                    }
                }
            }

            $cancelOrder = $spreadsheet->getSheet(1);
            $rowCancels = $cancelOrder->toArray();
            foreach ($rowCancels as $cellCancel) {
                if (intval($cellCancel[0]) > 0) {
                    $oldPoItemCancel = $cellCancel[10];
                    $cancelLoadplan = Loadplan::where('po_number', $oldPoItemCancel)->first();
                    if ($cancelLoadplan) {
                        $cancelLoadplan->delete();
                    }
                }
            }

            Notification::make()
                ->success()
                ->title('Recap divert ' . $this->fileName . '.')
                ->body('Recap divert successfully, please refresh page.')
                ->sendToDatabase($this->receipent);
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Recap divert ' . $this->fileName . '.')
                ->body($e->getMessage())
                ->sendToDatabase($this->receipent);
        }
        unlink($this->file);
    }
}
