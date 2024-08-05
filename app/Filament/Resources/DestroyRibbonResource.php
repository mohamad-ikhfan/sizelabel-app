<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestroyRibbonResource\Pages;
use App\Filament\Resources\DestroyRibbonResource\RelationManagers;
use App\Models\DestroyRibbon;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DestroyRibbonResource extends Resource
{
    protected static ?string $model = DestroyRibbon::class;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d-F-Y')
                    ->firstDayOfWeek(1)
                    ->locale('en')
                    ->closeOnDateSelection()
                    ->required(),

                Forms\Components\TextInput::make('qty')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),

                Forms\Components\Select::make('user_id')
                    ->label('PIC')
                    ->options(User::all()->pluck('name', 'id'))
                    ->columnSpanFull()
                    ->default(auth()->user()->id)
                    ->disabled(auth()->user()->id != 1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->disabledClick()
                    ->searchable()
                    ->sortable()
                    ->date('d-F-Y'),

                Tables\Columns\TextColumn::make('qty')
                    ->disabledClick()
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('user.name')
                    ->disabledClick()
                    ->label('PIC')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => auth()->user()->id == 1 ? false : $record->user_id !== auth()->user()->id),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => auth()->user()->id == 1 ? false : $record->user_id !== auth()->user()->id),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDestroyRibbons::route('/'),
        ];
    }
}