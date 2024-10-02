<x-filament-panels::page>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($this->getExchanges() as $exchange)

            <a href="{{ \App\Filament\Pages\TradingPairs::getUrl(['exchange' => $exchange]) }}"
               class="p-6 bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
            >
                <h5 class="text-red-500">
                    {{ $exchange }}
                </h5>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
