<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShoeResource\Pages;
use App\Filament\Resources\ShoeResource\RelationManagers;
use App\Models\Shoe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShoeResource extends Resource
{
    protected static ?string $model = Shoe::class;

    protected static ?string $navigationIcon = null;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('model_name')
                            ->required()
                            ->autocapitalize('uppercase')
                            ->extraInputAttributes(['class' => 'uppercase']),

                        Forms\Components\Select::make('gender')
                            ->required()
                            ->options([
                                "Mens" => "Mens",
                                "Womens" => "Womens",
                                "Boys Grade School" => "Boys Grade School",
                                "Grade School Unisex" => "Grade School Unisex",
                                "Boys Pre School" => "Boys Pre School",
                                "Pre School Unisex" => "Pre School Unisex",
                                "Boys Toddler" => "Boys Toddler",
                                "Toddler Unisex" => "Toddler Unisex",
                            ]),

                        Forms\Components\Select::make('group_size')
                            ->required()
                            ->options([
                                "M/W" => "M/W",
                                "GS" => "GS",
                                "PS" => "PS",
                                "TD" => "TD",
                            ]),

                        Forms\Components\Select::make('material')
                            ->required()
                            ->options(['Heatseal' => 'Heatseal', 'Poliyester' => 'Poliyester']),

                        Forms\Components\Select::make('measure')
                            ->required()
                            ->options([
                                "BIG (35x33)" => "BIG (35x33)",
                                "BIG (35x33) - SMALL (30x33)" => "BIG (35x33) - SMALL (30x33)",
                                "SMALL (30x33)" => "SMALL (30x33)",
                                "SMALL (30x21)" => "SMALL (30x21)"
                            ]),

                        Forms\Components\Select::make('wide')
                            ->options([1 => 'Yes', 0 => 'No'])
                            ->required(),
                    ])
                    ->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('model_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('gender')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('group_size')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('material')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('measure')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('wide')
                    ->sortable()
                    ->searchable()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel()
                    ->hidden(auth()->user()->id !== 1),
                Tables\Actions\DeleteAction::make()
                    ->hiddenLabel()
                    ->hidden(auth()->user()->id !== 1),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->hidden(auth()->user()->id !== 1),
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
            'index' => Pages\ListShoes::route('/'),
            'create' => Pages\CreateShoe::route('/create'),
            'edit' => Pages\EditShoe::route('/{record}/edit'),
        ];
    }
}