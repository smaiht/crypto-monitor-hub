<?php

namespace App\Filament\Resources\PriceAnalysisResource\Pages;

use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use App\Services\PriceAnalysisService;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\PriceAnalysisResource;

class AnalyzePrices extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PriceAnalysisResource::class;

    protected static string $view = 'filament.pages.analyze-prices';

    public $exchange;
    public $coin;
    public $datetime;
    public $result;
    public $isFormSubmitted = false;

    public function analyze()
    {
        $this->isFormSubmitted = true;
        $data = $this->form->getState();

        $service = new PriceAnalysisService();
        $this->result = $service->analyze($data['exchange'], $data['coin'], $data['datetime']);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exchange')
                    ->options(collect(explode(',', env('EXCHANGES')))->mapWithKeys(fn ($item) => [$item => $item]))
                    ->required()
                    ->native(false),

                Select::make('coin')
                    ->options(function () {
                        $pairs = json_decode(file_get_contents(app_path('Data/pairs.json')), true);
                        return collect($pairs)->mapWithKeys(fn ($pair) => [$pair => $pair]);
                    })
                    ->required()
                    ->native(false),

                DateTimePicker::make('datetime')
                    ->displayFormat('Y-m-d H:i:s')
                    ->format('Y-m-d H:i:s')
                    ->required()
                    ->native(false),
            ])->columns(3);
    }
}
