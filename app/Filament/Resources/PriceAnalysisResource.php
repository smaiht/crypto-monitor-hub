<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceAnalysisResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PriceAnalysisResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationGroup = 'Local SQL';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'index' => Pages\AnalyzePrices::route('/'),
        ];
    }
}
