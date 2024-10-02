<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    @livewireStyles
    @vite('resources/css/app.css')
</head>

<body>
    {{-- <script>
        const ws = new WebSocket('ws://{{ env('WS_HOST') }}:{{ env('WS_PORT') }}');

        ws.onopen = function() {
            console.log('Connected to the WebSocket server');
        };

        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            console.log('Received price update:', data);
        };

        ws.onerror = function(error) {
            console.error('WebSocket error:', error);
        };

        ws.onclose = function() {
            console.log('Disconnected from the WebSocket server');
        };
    </script> --}}

    @livewireScripts
    @vite('resources/js/app.js')
</body>

</html>
