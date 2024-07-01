<?php

namespace App\Filament\Resources\SchedulePrintResource\Pages;

use App\Filament\Resources\SchedulePrintResource;
use App\Models\Loadplan;
use App\Models\ReportPrint;
use App\Models\SchedulePrint;
use App\Models\Shoe;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListSchedulePrints extends ListRecords
{
    protected static string $resource = SchedulePrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Sync to printed')
                ->color('primary')
                ->action(function () {
                    $schedulePrints = SchedulePrint::all();
                    foreach ($schedulePrints as $schedulePrint) {
                        $reportPrintGroups = ReportPrint::select('line', 'release', 'style_number')
                            ->groupBy('line', 'release', 'style_number')
                            ->where([
                                'line' => $schedulePrint->line,
                                'release' => $schedulePrint->release,
                                'style_number' => $schedulePrint->style_number,
                            ])
                            ->get();

                        foreach ($reportPrintGroups as $reportPrintGroup) {
                            $printDates = collect();
                            $qtyTotals = collect();
                            foreach (ReportPrint::where($reportPrintGroup->toArray())->get() as $reportPrint) {
                                $printDates->push($reportPrint->print_date);
                                $qtyTotals->push($reportPrint->qty_total);
                                $printedBy = $reportPrint->user_id;
                            }

                            if ($schedulePrint->qty == $qtyTotals->sum()) {
                                $schedulePrint->update([
                                    'status' => 'printed',
                                    'status_updated_at' => $printDates->max(),
                                    'status_updated_by_user_id' => isset($printedBy) ? $printedBy : null
                                ]);
                            } elseif ($qtyTotals->sum() > $schedulePrint->qty) {
                                $schedulePrint->update([
                                    'status' => 'printed',
                                    'status_updated_at' => $printDates->max(),
                                    'status_updated_by_user_id' => isset($printedBy) ? $printedBy : null
                                ]);
                            } elseif ($qtyTotals->sum() < $schedulePrint->qty) {
                                $schedulePrint->update([
                                    'status' => 'printing',
                                    'status_updated_at' => $printDates->max(),
                                    'status_updated_by_user_id' => isset($printedBy) ? $printedBy : null
                                ]);
                            }
                        }
                    }
                })
                ->hidden(auth()->user()->id !== 1),

            Actions\Action::make('Refresh material')
                ->color('primary')
                ->action(function () {
                    $schedulePrints = SchedulePrint::whereNull('shoe_id')->get();
                    foreach ($schedulePrints as $schedulePrint) {
                        $shoe = Shoe::where('model_name', $schedulePrint->model_name)->first();
                        if ($shoe) {
                            $schedulePrint->update([
                                'shoe_id' => $shoe->id
                            ]);
                        }
                    }
                })
                ->hidden(auth()->user()->id !== 1),

            Actions\Action::make('New schedule print')
                ->color('primary')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Save')
                ->form(function (Forms\Form $form) {
                    return $form
                        ->schema([
                            Forms\Components\Select::make('from_release')
                                ->options(function (Loadplan $loadplan) {
                                    $loadplans = $loadplan->all()->pluck('release')->toArray();
                                    rsort($loadplans);
                                    $options = [];
                                    foreach ($loadplans as $value) {
                                        $options[$value] = now()->parse($value)->format('m/d y');
                                    }
                                    return $options;
                                })
                                ->searchable()
                                ->required()
                        ]);
                })
                ->action(function (array $data) {
                    $loadplansByReleases = Loadplan::select('line', 'release', 'style_number', 'model_name')
                        ->groupBy('line', 'release', 'style_number', 'model_name')
                        ->where('release', '>=', $data['from_release'])
                        ->get();

                    $data['schedules'] = [];

                    foreach ($loadplansByReleases as $loadplansByRelease) {
                        $spkPublishes = collect();
                        $qtyOrigins = collect();
                        $loadplans = Loadplan::where($loadplansByRelease->toArray())->get();
                        foreach ($loadplans as $loadplan) {
                            if (!is_numeric(strpos(strtolower($loadplan->remark), 'jx2', 0)) && !is_numeric(strpos(strtolower($loadplan->remark), 'pm', 0)) && !is_numeric(strpos(strtolower($loadplan->remark), 'cancel', 0))) {
                                if (!empty($loadplan->spk_publish)) {
                                    $spkPublishes->push(now()->parse($loadplan->spk_publish)->getTimestamp());
                                }
                                $qtyOrigins->push($loadplan->qty_origin);
                            }
                        }

                        $schedule = null;
                        if (isset($spkPublishes) && !empty($spkPublishes->avg())) {
                            $file = Storage::get('public/HolidayCalendarID.json');
                            $schedule = now()->parse(date('Y-m-d', $spkPublishes->avg()));
                            foreach (json_decode($file ?? [], true) as $res) {
                                if ($res['date'] == $schedule->format('Y-m-d')) {
                                    $schedule->subDay();
                                }
                            }
                            while ($schedule->isWeekend()) {
                                $schedule->subDay();
                            }
                        }

                        $shoe = Shoe::where('model_name', $loadplansByRelease->model_name)->first();

                        $data['schedules'][] = [
                            'line' => $loadplansByRelease->line,
                            'schedule' => $schedule,
                            'release' => $loadplansByRelease->release,
                            'style_number' => $loadplansByRelease->style_number,
                            'model_name' => $loadplansByRelease->model_name,
                            'qty' => $qtyOrigins->sum(),
                            'shoe_id' => $shoe ? $shoe->id : null
                        ];
                    }

                    $printings = SchedulePrint::where('status', 'printing')->get()->toArray();
                    SchedulePrint::truncate();
                    foreach ($data['schedules'] as $value) {
                        if ($value['qty'] > 0) {
                            SchedulePrint::create($value);
                        }
                    }
                    foreach ($printings as $printing) {
                        $NewSchedulePrint = SchedulePrint::where([
                            'line' => $printing['line'],
                            'release' => $printing['release'],
                            'style_number' => $printing['style_number']
                        ])->first();

                        $NewSchedulePrint->update([
                            'status' => $printing['status'],
                            'status_updated_at' => $printing['status_updated_at'],
                            'status_updated_by_user_id' => $printing['status_updated_by_user_id']
                        ]);
                    }

                    Notifications\Notification::make()
                        ->success()
                        ->title('Schedule created.')
                        ->send();
                })
                ->hidden(auth()->user()->id !== 1)
        ];
    }
}