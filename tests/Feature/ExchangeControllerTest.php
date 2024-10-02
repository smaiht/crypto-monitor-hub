<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\PriceAnalysisService;
use Mockery;

class ExchangeControllerTest extends TestCase
{
    public function testGetPriceReturnsCorrectData()
    {
        $mockService = Mockery::mock(PriceAnalysisService::class);
        $mockService->shouldReceive('analyze')
            ->once()
            ->with('okx', 'BTC/USDT', '2024-10-01 08:00:00')
            ->andReturn([
                'time' => '2024-10-01 04:49:55+09',
                'symbol' => 'BTC/USDT',
                'bid_min' => '63536.1',
                'bid_max' => '63536.8',
                'ask_min' => '63536.9',
                'ask_max' => '63536.9',
            ]);

        $this->app->instance(PriceAnalysisService::class, $mockService);

        $response = $this->getJson('/price?exchange=okx&symbol=BTC/USDT&datetime=2024-10-01 08:00:00');

        $response->assertStatus(200)
            ->assertJson([
                'time' => '2024-10-01 04:49:55+09',
                'symbol' => 'BTC/USDT',
                'bid_min' => '63536.1',
                'bid_max' => '63536.8',
                'ask_min' => '63536.9',
                'ask_max' => '63536.9',
            ]);
    }

    public function testGetPriceReturns404WhenNoData()
    {
        $mockService = Mockery::mock(PriceAnalysisService::class);
        $mockService->shouldReceive('analyze')
            ->once()
            ->with('okx', 'BTC/USDT', '2024-10-01 08:00:00')
            ->andReturnNull();

        $this->app->instance(PriceAnalysisService::class, $mockService);

        $response = $this->getJson('/price?exchange=okx&symbol=BTC/USDT&datetime=2024-10-01 08:00:00');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No data found'
            ]);
    }

    public function testGetPriceValidatesInput()
    {
        $response = $this->getJson('/price');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['exchange', 'symbol', 'datetime']);
    }

    public function testGetPriceReturns422ForInvalidExchange()
    {
        $response = $this->getJson('/price?exchange=invalid&symbol=BTC/USDT&datetime=2024-10-01 08:00:00');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['exchange']);
    }

    public function testGetPriceValidatesDateFormat()
    {
        $response = $this->getJson('/price?exchange=okx&symbol=BTC/USDT&datetime=2024-10-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['datetime']);
    }

}
