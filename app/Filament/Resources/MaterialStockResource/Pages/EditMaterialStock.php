<?php

namespace App\Filament\Resources\MaterialStockResource\Pages;

use App\Filament\Resources\MaterialStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaterialStock extends EditRecord
{
    protected static string $resource = MaterialStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
