<x-filament-panels::page>
    <div>
        @if ($this->exchange)
            <h2>Top 100 Pairs for {{ $this->exchange }}</h2>
        @else
            <h2>Top 100 Pairs from All Exchanges</h2>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($this->top100Pairs as $pair)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="font-bold text-xl mb-2 text-gray-900 dark:text-white">{{ $pair }}</div>
                <div class="text-gray-700 dark:text-gray-400 text-base">
                    <div class="flex justify-between">
                        <span class="">Buy Price:</span>
                        <span class="buy-price" data-pair="{{ $pair }}"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="">Sell Price:</span>
                        <span class="sell-price" data-pair="{{ $pair }}"></span>
                    </div>
                    @if (!$this->exchange)
                        <div class="flex justify-between">
                            <span class="">Exchange:</span>
                            <span class="exchange" data-pair="{{ $pair }}"></span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script>
                const ws = new WebSocket('ws://{{ env('WS_HOST') }}:{{ env('WS_PORT') }}');
                const currentExchange = '{{ $this->exchange }}';

                ws.onopen = function() {
                    console.log('Connected to the WebSocket server');
                };

                ws.onerror = function(error) {
                    console.error('WebSocket error:', error);
                };

                ws.onclose = function() {
                    console.log('Disconnected from the WebSocket server');
                };

                ws.onmessage = function (event) {
                    let data = JSON.parse(event.data);

                    if (currentExchange && data.exchange != currentExchange) {
                        return;
                    }

                    console.log('Received price update:', data);

                    for (const symbol in data.tickers) {
                        const tickerData = data.tickers[symbol];
                        const buyPriceElement = document.querySelector(`.buy-price[data-pair="${symbol}"]`);
                        const sellPriceElement = document.querySelector(`.sell-price[data-pair="${symbol}"]`);
                        const exchangeElement = document.querySelector(`.exchange[data-pair="${symbol}"]`);

                        if (buyPriceElement) {
                            buyPriceElement.innerText = tickerData.bid;
                        }

                        if (sellPriceElement) {
                            sellPriceElement.innerText = tickerData.ask;
                        }

                        if (exchangeElement) {
                            exchangeElement.innerText = data.exchange;
                        }
                    }


                };
        </script>
    @endpush
</x-filament-panels::page>
