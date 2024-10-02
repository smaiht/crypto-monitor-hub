<x-filament::page>
    <div class="space-y-6">
        <form wire:submit.prevent="analyze" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Get Historic Data
                </x-filament::button>
            </div>
        </form>

        @if($result)
            <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h2 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Results</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-semibold">Bid Min:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $result['bid_min'] }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Bid Max:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $result['bid_max'] }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Ask Min:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $result['ask_min'] }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Ask Max:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $result['ask_max'] }}</span>
                    </div>
                </div>
            </div>
        @elseif($this->isFormSubmitted)
            <div class="mt-6 p-4 bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 rounded-lg">
                No data available for the selected criteria.
            </div>
        @endif
    </div>
</x-filament::page>
