<?php

namespace App\Filament\Resources\MaterialStockResource\Pages;

use App\Filament\Resources\MaterialStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMaterialStock extends CreateRecord
{
    protected static string $resource = MaterialStockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        foreach ($data['materials'] as $material) {
            $materialStock = $this->getModel()::where('material_id', $material['material_id'])->latest('date')->first();

            $dataMaterial = [
                'date' => $data['date'],
                'material_id' => $material['material_id'],
                'first_stock' => floatval($materialStock->last_stock ?? 0),
                'status' => $data['status'],
                'qty' => floatval($material['qty']),
                'last_stock' => floatval($data['status'] == 'in' ? ($materialStock->last_stock ?? 0 + $material['qty']) : $materialStock->last_stock ?? 0 - $material['qty']),
                'remarks' => $material['remarks'],
                'user_id' => auth()->user()->id
            ];

            $record = $this->getModel()::create($dataMaterial);
        }

        return $record;
    }
}