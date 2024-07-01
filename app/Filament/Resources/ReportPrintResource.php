<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportPrintResource\Pages;
use App\Filament\Resources\ReportPrintResource\RelationManagers;
use App\Models\ReportPrint;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportPrintResource extends Resource
{
    protected static ?string $model = ReportPrint::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()
                    ->schema([
                        Forms\Components\DatePicker::make('print_date')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->required(),

                        Forms\Components\TextInput::make('line')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Forms\Components\TextInput::make('po_number')
                            ->numeric()
                            ->minLength(10)
                            ->nullable(),

                        Forms\Components\DatePicker::make('release')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->required(),

                        Forms\Components\TextInput::make('style_number')
                            ->string()
                            ->required(),

                        Forms\Components\TextInput::make('model_name')
                            ->string()
                            ->required(),

                        Forms\Components\TextInput::make('special')
                            ->string()
                            ->required()
                            ->default('-'),

                        Forms\Components\TextInput::make('qty_total')
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),

                        Forms\Components\Textarea::make('remark')
                            ->string()
                            ->required()
                            ->default('-'),

                        Forms\Components\Select::make('user_id')
                            ->label('Printed by')
                            ->options(User::all()->pluck('name', 'id'))
                            ->nullable()
                    ])

            ])
            ->disabled(auth()->user()->id !== 1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('print_date', 'desc')
            ->recordUrl(null)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('print_date')
                    ->date('d F Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('line')
                    ->sortable(),

                Tables\Columns\TextColumn::make('po_number')
                    ->sortable(),

                Tables\Columns\TextColumn::make('release')
                    ->date('m/d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('style_number')
                    ->sortable(),

                Tables\Columns\TextColumn::make('model_name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('special')
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty_total')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('remark')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Printed by')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('print_date')
                    ->searchable()
                    ->options(function (ReportPrint $reportPrint) {
                        $options = [];
                        $reportPrints = $reportPrint->all()->pluck('print_date')->toArray();
                        sort($reportPrints);
                        foreach ($reportPrints as $value) {
                            $options[$value] = now()->parse($value)->format('d F Y');
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('line')
                    ->searchable()
                    ->options(function (ReportPrint $reportPrint) {
                        $options = [];
                        $reportPrints = $reportPrint->all()->pluck('line')->toArray();
                        sort($reportPrints);
                        foreach ($reportPrints as $value) {
                            if (!empty($value)) {
                                $options[$value] = $value;
                            }
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('release')
                    ->searchable()
                    ->options(function (ReportPrint $reportPrint) {
                        $options = [];
                        $loadplans = $reportPrint->all()->pluck('release')->toArray();
                        sort($loadplans);
                        foreach ($loadplans as $value) {
                            $options[$value] = now()->parse($value)->format('m/d');
                        }
                        return $options;
                    }),
                Tables\Filters\SelectFilter::make('style_number')
                    ->searchable()
                    ->options(function (ReportPrint $reportPrint) {
                        $options = [];
                        $loadplans = $reportPrint->all()->pluck('style_number')->toArray();
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
                    ->options(function (ReportPrint $reportPrint) {
                        $options = [];
                        $reportPrints = $reportPrint->all()->pluck('model_name')->toArray();
                        sort($reportPrints);
                        foreach ($reportPrints as $value) {
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
            'index' => Pages\ListReportPrints::route('/'),
            'edit' => Pages\EditReportPrint::route('/{record}/edit'),
        ];
    }
}