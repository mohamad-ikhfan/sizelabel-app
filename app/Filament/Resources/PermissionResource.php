<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return auth()->user()->can('view-any-permission');
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('view-permission');
    // }

    // public static function canCreate(): bool
    // {
    //     return auth()->user()->can('create-permission');
    // }

    // public static function canEdit(): bool
    // {
    //     return auth()->user()->can('edit-permission');
    // }

    // public static function canDelete(): bool
    // {
    //     return auth()->user()->can('delete-permission');
    // }

    // public static function canRestore(): bool
    // {
    //     return auth()->user()->can('restore-permission');
    // }

    // public static function canForceDelete(): bool
    // {
    //     return auth()->user()->can('force-delete-permission');
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->string()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('guard_name')
                            ->default('web')
                            ->readOnly()
                    ])
                    ->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
