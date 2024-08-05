<?php

namespace App\Filament\Resources\DestroyRibbonResource\Pages;

use App\Exports\DestroyRibbonExport;
use App\Filament\Resources\DestroyRibbonResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Forms;

class ManageDestroyRibbons extends ManageRecords
{
    protected static string $resource = DestroyRibbonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export-log-destroy')
                ->color('gray')
                ->form([
                    Forms\Components\DatePicker::make('from')
                        ->required()
                        ->native(false)
                        ->displayFormat('d-F-Y')
                        ->closeOnDateSelection(),

                    Forms\Components\DatePicker::make('to')
                        ->required()
                        ->native(false)
                        ->displayFormat('d-F-Y')
                        ->closeOnDateSelection(),
                ])
                ->action(function (array $data) {
                    $from = now()->parse($data['from']);
                    $to = now()->parse($data['to']);
                    $fileName = 'log_destroy_ribbon_(From-' . $from->format('d-m-y') . '_to-' . $to->format('d-m-y') . ')_' . time() . '.xlsx';
                    return (new DestroyRibbonExport($data['from'], $data['to']))->download($fileName);
                })
                ->modalWidth('md')
                ->modalSubmitActionLabel('Export'),

            Actions\CreateAction::make()
                ->action(function (Actions\CreateAction $createAction, array $data) {
                    $data['user_id'] = $data['user_id'] ?? auth()->user()->id;
                    $destroyRibbon = $this->getModel()::where('date', $data['date'])
                        ->where('user_id', $data['user_id'])
                        ->first();

                    if ($destroyRibbon) {
                        $destroyRibbon->update(['qty' => ($destroyRibbon->qty + $data['qty'])]);
                    } else {
                        $this->getModel()::create($data);
                    }
                    $createAction->success();
                }),
        ];
    }
}