<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialStockResource\Pages;
use App\Filament\Resources\MaterialStockResource\RelationManagers;
use App\Models\Material;
use App\Models\MaterialStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialStockResource extends Resource
{
    protected static ?string $model = MaterialStock::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Materials';

    public static function canAccess(): bool
    {
        return auth()->user()->can('view-any-material-stock');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DateTimePicker::make('date')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d-F-Y')
                            ->firstDayOfWeek(1)
                            ->locale('en')
                            ->closeOnDateSelection()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(['in' => 'Material in', 'out' => 'Material out'])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('material_id')
                            ->label('Material')
                            ->options(Material::all()->pluck('name', 'id'))
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('qty')
                            ->required()
                            ->numeric()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state) {
                                $materialStock = MaterialStock::where('material_id', $get('material_id'))->latest('date')->first();
                                if (!empty($materialStock)) {
                                    $set('first_stock', $materialStock->last_stock);
                                    if ($get('status') == 'in') {
                                        $set('last_stock', $materialStock->last_stock + $state);
                                    }
                                    if ($get('status') == 'out') {
                                        $set('last_stock', $materialStock->last_stock - $state);
                                    }
                                } else {
                                    $set('first_stock', 0);
                                    $set('last_stock', $state);
                                }
                            })
                            ->live()
                            ->readOnly(fn(Forms\Get $get) => empty($get('material_id')) && empty($get('status'))),
                        Forms\Components\Textarea::make('remarks')
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('first_stock'),
                        Forms\Components\Hidden::make('last_stock'),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->user()->id),
                    ])
                    ->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->disabledClick()
                    ->searchable()
                    ->sortable()
                    ->date('d-F-Y'),
                Tables\Columns\TextColumn::make('material.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_stock')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_stock')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('PIC')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => auth()->user()->id == 1 ? false : $record->user_id !== auth()->user()->id),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => auth()->user()->id == 1 ? false : $record->user_id !== auth()->user()->id),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialStocks::route('/'),
            'create' => Pages\CreateMaterialStock::route('/create'),
            'edit' => Pages\EditMaterialStock::route('/{record}/edit'),
        ];
    }
}