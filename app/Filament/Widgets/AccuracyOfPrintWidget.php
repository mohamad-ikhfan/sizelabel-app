<?php

namespace App\Filament\Widgets;

use App\Models\AccuracyOfPrint;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use RyanChandler\FilamentProgressColumn\ProgressColumn;

class AccuracyOfPrintWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(AccuracyOfPrint::query())
            ->defaultSort('schedule', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('schedule')
                    ->date('d-F-Y'),
                Tables\Columns\TextColumn::make('print_date')
                    ->date('d-F-Y'),
                ProgressColumn::make('accuracy'),
            ]);
    }
}