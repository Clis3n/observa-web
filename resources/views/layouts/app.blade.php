<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'OBSERVA') }}</title>

        <!-- Ikon (Favicon) & Font Awesome -->
        <link rel="icon" href="{{ asset('landing_assets/image/icon.svg') }}" type="image/svg+xml">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Fonts & Styles -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css' rel='stylesheet' />

        <!-- Vite (Tailwind CSS & JS) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            body {
                font-family: 'Poppins', sans-serif;
                /* PERBAIKAN: Mencegah scroll horizontal dan vertikal pada seluruh halaman */
                overflow: hidden;
            }
            .mapboxgl-popup-content { padding: 10px 15px; font-family: 'Poppins', sans-serif; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; }
            .h-screen-minus-header { height: calc(100vh - 64px); }
            .mapboxgl-ctrl-logo, .mapboxgl-ctrl-attrib { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        <script src='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js'></script>
        @stack('scripts')
    </body>
</html>
