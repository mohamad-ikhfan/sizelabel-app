<?php

namespace App\Filament\Resources\LoadplanResource\Pages;

use App\Filament\Resources\LoadplanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoadplan extends EditRecord
{
    protected static string $resource = LoadplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(auth()->user()->id !== 1),
        ];
    }
}