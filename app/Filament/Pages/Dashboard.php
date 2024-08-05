<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = '';

    public function getColumns(): int | string | array
    {
        return 1;
    }
}