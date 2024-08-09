<?php

namespace App\Filament\Resources\MaterialStockResource\Pages;

use App\Filament\Resources\MaterialStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaterialStock extends CreateRecord
{
    protected static string $resource = MaterialStockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}