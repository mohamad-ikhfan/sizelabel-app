<?php

namespace App\Filament\Resources\ReportPrintResource\Pages;

use App\Filament\Resources\ReportPrintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportPrint extends EditRecord
{
    protected static string $resource = ReportPrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(auth()->user()->id !== 1),
        ];
    }
}