<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoadplanResource\Pages;
use App\Filament\Resources\LoadplanResource\RelationManagers;
use App\Models\Loadplan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoadplanResource extends Resource
{
    protected static ?string $model = Loadplan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('line')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        Forms\Components\DatePicker::make('spk_publish')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->nullable(),

                        Forms\Components\DatePicker::make('release')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->required(),

                        Forms\Components\DatePicker::make('doc_date')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->nullable(),

                        Forms\Components\TextInput::make('po_number')
                            ->numeric()
                            ->minLength(10)
                            ->required(),

                        Forms\Components\TextInput::make('style_number')
                            ->string()
                            ->required(),

                        Forms\Components\TextInput::make('model_name')
                            ->string()
                            ->required(),

                        Forms\Components\TextInput::make('invoice')
                            ->string()
                            ->nullable(),

                        Forms\Components\TextInput::make('destination')
                            ->string()
                            ->nullable(),

                        Forms\Components\DatePicker::make('ogac')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->nullable(),

                        Forms\Components\TextInput::make('qty_origin')
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),

                        Forms\Components\TextInput::make('special')
                            ->string()
                            ->required()
                            ->default('-'),

                        Forms\Components\Textarea::make('remark')
                            ->string()
                            ->columnSpanFull()
                            ->required()
                            ->default('-'),
                    ])
                    ->columns(),
            ])
            ->disabled(auth()->user()->id !== 1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('line')
                    ->sortable(),

                Tables\Columns\TextColumn::make('spk_publish')
                    ->date('m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('release')
                    ->date('m/d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('doc_date')
                    ->date('m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('po_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('style_number')
                    ->sortable(),

                Tables\Columns\TextColumn::make('model_name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('destination')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ogac')
                    ->date('m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('qty_origin')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('special')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remark')
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('line')
                    ->searchable()
                    ->options(function (Loadplan $loadplan) {
                        $options = [];
                        $loadplans = $loadplan->all()->pluck('line')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            if (!empty($value)) {
                                $options[$value] = $value;
                            }
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('release')
                    ->searchable()
                    ->options(function (Loadplan $loadplan) {
                        $options = [];
                        $loadplans = $loadplan->all()->pluck('release')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            $options[$value] = now()->parse($value)->format('m/d');
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('style_number')
                    ->searchable()
                    ->options(function (Loadplan $loadplan) {
                        $options = [];
                        $loadplans = $loadplan->all()->pluck('style_number')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            if (!empty($value)) {
                                $options[$value] = $value;
                            }
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('model_name')
                    ->searchable()
                    ->options(function (Loadplan $loadplan) {
                        $options = [];
                        $loadplans = $loadplan->all()->pluck('model_name')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            if (!empty($value)) {
                                $options[$value] = $value;
                            }
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('special')
                    ->searchable()
                    ->options(function (Loadplan $loadplan) {
                        $options = [];
                        $loadplans = $loadplan->all()->pluck('special')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            if (!empty($value)) {
                                $options[$value] = $value;
                            }
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('remark')
                    ->searchable()
                    ->options(function (Loadplan $loadplan) {
                        $options = [];
                        $loadplans = $loadplan->all()->pluck('remark')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            if (!empty($value)) {
                                $options[$value] = $value;
                            }
                        }
                        return $options;
                    }),
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
            'index' => Pages\ListLoadplans::route('/'),
            'edit' => Pages\EditLoadplan::route('/{record}/edit'),
        ];
    }
}