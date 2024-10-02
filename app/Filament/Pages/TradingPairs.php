<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TradingPairs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Crypto';

    protected static string $view = 'filament.pages.trading-pairs';

    protected static ?string $slug = 'trading-pairs/{exchange?}';

    public ?string $exchange = null;
    public $top100Pairs = null;

    public function mount($exchange = null): void
    {
        $this->exchange = $exchange;

        $this->top100Pairs = json_decode(file_get_contents(app_path('Data/' . 'pairs.json')), true);
    }

    public function getTitle(): string
    {
        return $this->exchange ? "{$this->exchange} Trading Pairs" : 'Trading Pairs';
    }
}
