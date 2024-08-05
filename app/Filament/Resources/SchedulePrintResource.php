<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchedulePrintResource\Pages;
use App\Filament\Resources\SchedulePrintResource\RelationManagers;
use App\Models\SchedulePrint;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchedulePrintResource extends Resource
{
    protected static ?string $model = SchedulePrint::class;

    protected static ?string $navigationIcon = null;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Fieldset::make('Schedule')
                            ->schema([
                                Forms\Components\TextInput::make('line')
                                    ->required()
                                    ->numeric(),

                                Forms\Components\DatePicker::make('schedule')
                                    ->nullable(),

                                Forms\Components\DatePicker::make('release')
                                    ->required(),

                                Forms\Components\TextInput::make('style_number')
                                    ->required(),

                                Forms\Components\TextInput::make('model_name')
                                    ->required(),

                                Forms\Components\TextInput::make('qty')
                                    ->required()
                                    ->numeric(),
                            ]),
                        Forms\Components\Fieldset::make('Status Print')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'printing' => 'Printing',
                                        'printed' => 'Printed',
                                    ])
                                    ->nullable(),

                                Forms\Components\DatePicker::make('status_updated_at')
                                    ->nullable(),

                                Forms\Components\Select::make('status_updated_by_user_id')
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->nullable()
                                    ->columnSpanFull()
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('schedule')
            ->striped()
            ->poll(interval: '10s')
            ->defaultPaginationPageOption(25)
            ->recordUrl(null)
            ->columns([
                Tables\Columns\ColumnGroup::make('Schedule')
                    ->columns([
                        Tables\Columns\TextColumn::make('line'),
                        Tables\Columns\TextColumn::make('schedule')
                            ->date('d-F-Y'),
                        Tables\Columns\TextColumn::make('release')
                            ->date('m/d'),
                        Tables\Columns\TextColumn::make('style_number'),
                        Tables\Columns\TextColumn::make('model_name')
                            ->wrap(),
                        Tables\Columns\TextColumn::make('qty')
                            ->numeric(),
                    ])
                    ->alignCenter(),
                Tables\Columns\ColumnGroup::make('Label Material')
                    ->columns([
                        Tables\Columns\TextColumn::make('shoe.material')
                            ->label('Material'),
                        Tables\Columns\TextColumn::make('shoe.measure')
                            ->label('Size')
                            ->wrap(),
                    ])
                    ->alignCenter(),
                Tables\Columns\ColumnGroup::make('Status Print')
                    ->columns([
                        Tables\Columns\TextColumn::make('status')
                            ->formatStateUsing(fn ($record) => ucfirst($record->status) . ' (' . now()->parse($record->status_updated_at)->format('d/m/y') . ')')
                            ->badge()
                            ->color(fn ($state): string => $state == 'printed' ? 'success' : 'warning'),
                        Tables\Columns\TextColumn::make('status_updated_by_user.name')
                            ->label('Printed by')
                            ->wrap()
                    ])
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['printing' => 'Printing', 'printed' => 'Printed'])
                    ->modifyQueryUsing(function (Builder $query, $data) {
                        if ($data['value'] == 'printing') {
                            return $query->where('status', 'printing')->orWhere('status_updated_at', null);
                        } elseif ($data['value'] == 'printed') {
                            return $query->where('status', 'printed');
                        } else {
                            return $query;
                        }
                    })
                    ->default('printing'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'printing',
                            'status_updated_at' => now()->format('Y-m-d'),
                            'status_updated_by_user_id' => auth()->user()->id ?? null
                        ]);
                    })
                    ->icon('heroicon-o-document-check')
                    ->visible(auth()->user()->can('schedule-printing')),
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
                ]),
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
            'index' => Pages\ListSchedulePrints::route('/'),
            'edit' => Pages\EditSchedulePrint::route('/{record}/edit'),
        ];
    }
}