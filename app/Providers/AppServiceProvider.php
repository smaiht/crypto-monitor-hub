<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // dynamically add DB connections for each of the exchanges
        $exchanges = explode(',', env('EXCHANGES'));

        foreach ($exchanges as $exchange) {
            $uppercaseExchange = strtoupper($exchange);

            config(["database.connections.{$exchange}" => [
                'driver' => 'pgsql',
                'host' => env("DB_{$uppercaseExchange}_HOST"),
                'port' => env("DB_{$uppercaseExchange}_PORT"),
                'database' => env("DB_{$uppercaseExchange}_DATABASE"),
                'username' => env("DB_{$uppercaseExchange}_USERNAME"),
                'password' => env("DB_{$uppercaseExchange}_PASSWORD"),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]]);
        }
    }
}
