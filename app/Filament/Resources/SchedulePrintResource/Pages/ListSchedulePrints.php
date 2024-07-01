<?php

namespace App\Filament\Resources\SchedulePrintResource\Pages;

use App\Filament\Resources\SchedulePrintResource;
use App\Models\CalendarHolidayId;
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
                ->color('gray')
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
                ->color('gray')
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
                                    if (count($loadplans) > 0) {
                                        rsort($loadplans);
                                    }
                                    $options = [];
                                    foreach ($loadplans as $value) {
                                        $options[$value] = now()->parse($value)->format('m/d y');
                                    }
                                    return $options;
                                })
                                ->searchable()
                                ->required()
                                ->live(),

                            Forms\Components\Select::make('except_remarks')
                                ->options(function (Loadplan $loadplan, Forms\Get $get) {
                                    $options = [];
                                    if ($get('from_release')) {
                                        $loadplans = $loadplan->where('release', '>=', $get('from_release'))
                                            ->get()
                                            ->pluck('remark')
                                            ->toArray();
                                        if (count($loadplans) > 0) {
                                            sort($loadplans);
                                        }
                                        foreach ($loadplans as $value) {
                                            $options[$value] = $value;
                                        }
                                    }
                                    return $options;
                                })
                                ->multiple()
                                ->nullable(),
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
                        $loadplans = Loadplan::WhereNotIn('remark', $data['except_remarks'])->where($loadplansByRelease->toArray())->get();
                        foreach ($loadplans as $loadplan) {
                            if (!empty($loadplan->spk_publish)) {
                                $spkPublishes->push(now()->parse($loadplan->spk_publish)->getTimestamp());
                            }
                            $qtyOrigins->push($loadplan->qty_origin);
                        }

                        $schedule = null;
                        if (isset($spkPublishes) && !empty($spkPublishes->avg())) {
                            $schedule = now()->parse(date('Y-m-d', $spkPublishes->avg()));
                            foreach (CalendarHolidayId::all() as $calendar) {
                                if ($calendar->date == $schedule->format('Y-m-d')) {
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
