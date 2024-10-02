<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ListExchanges extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Exchanges';
    protected static ?string $navigationGroup = 'Crypto';

    protected static string $view = 'filament.pages.list-exchanges';

    public function getExchanges(): array
    {
        return explode(',', env('EXCHANGES'));
    }

}
