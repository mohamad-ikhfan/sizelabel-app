<?php

namespace App\Filament\Resources\SchedulePrintResource\Pages;

use App\Filament\Resources\SchedulePrintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchedulePrint extends EditRecord
{
    protected static string $resource = SchedulePrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(auth()->user()->id !== 1),
        ];
    }
}