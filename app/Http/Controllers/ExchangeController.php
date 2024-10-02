<?php

namespace App\Http\Controllers;

use ccxt\okx;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use ccxt\Exchange;
use Illuminate\Validation\Rule;
use App\Services\PriceAnalysisService;

class ExchangeController extends Controller
{
    protected $priceAnalysisService;

    public function __construct(PriceAnalysisService $priceAnalysisService)
    {
        $this->priceAnalysisService = $priceAnalysisService;
    }


    public function getTop100Coins(string $exchangeName = 'okx', string $quote = 'usdt')
    {
        $exchangeClass = "\\ccxt\\$exchangeName";

        $exchange = new $exchangeClass();

        $tickers = $exchange->fetchTickers();

        $quotePairs = array_filter(array_keys($tickers), function ($symbol) use ($quote) {
            return strpos($symbol, '/' . strtoupper($quote)) !== false;
        });

        $quoteTickers = array_intersect_key($tickers, array_flip($quotePairs));

        uasort($quoteTickers, function ($a, $b) {
            return $b['quoteVolume'] - $a['quoteVolume'];
        });

        $top100Pairs = array_slice(array_keys($quoteTickers), 0, 100);
        file_put_contents(app_path('Data/' . 'pairs.json'), json_encode($top100Pairs));

        echo 'Data has been saved to Data/pairs.json';

        dd($top100Pairs);
    }

    public function getPrice(Request $request)
    {
        $validExchanges = explode(',', env('EXCHANGES', ''));

        try {
            $validated = $request->validate([
                'exchange' => ['required', 'string', Rule::in($validExchanges)],
                'symbol' => 'required|string',
                'datetime' => 'required|date_format:Y-m-d H:i:s',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $result = $this->priceAnalysisService->analyze(
            $validated['exchange'],
            $validated['symbol'],
            $validated['datetime']
        );

        if (!$result) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json($result);
    }


}
