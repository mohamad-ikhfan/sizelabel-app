<?php

namespace App\Filament\Resources\MaterialStockResource\Pages;

use App\Filament\Resources\MaterialStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterialStocks extends ListRecords
{
    protected static string $resource = MaterialStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
