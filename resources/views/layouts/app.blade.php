<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles & Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css' rel='stylesheet' />
        <style>
            .h-screen-minus-header { height: calc(100vh - 65px); } /* 65px for standard navigation height */
            .mapboxgl-popup-content { padding: 10px 15px; font-family: 'Figtree', sans-serif; }
        </style>

        <!-- Scripts -->
        <script src='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js'></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts

        <!-- ======================================================================= -->
        <!-- PUSAT KENDALI PETA - SEMUA LOGIKA JAVASCRIPT ADA DI SINI                -->
        <!-- ======================================================================= -->
        <script>
            // Variabel global untuk menyimpan instance peta dan marker
            window.mapInstance = null;
            window.mapMarkers = {}; // Gunakan object untuk mapping ID -> marker

            document.addEventListener('DOMContentLoaded', () => {
                const mapElement = document.getElementById('map');
                if (!mapElement) return;
                if (window.mapInstance) return;

                mapboxgl.accessToken = 'pk.eyJ1IjoiY2xpc2VuYXJkeWxha3Nvbm93aWNha3Nvbm8iLCJhIjoiY20zc25wbjFnMGZ2eTJxc2ZhY2JkZjZ5ayJ9.MxI1flsCYndt7aXbImXMQw'; // GANTI TOKEN ANDA

                window.mapInstance = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: [110.3695, -7.7956],
                    zoom: 9
                });

                window.mapInstance.on('load', () => {
                    console.log('Peta diinisialisasi dari layout.');
                    Livewire.dispatch('getInitialData');
                });
            });

            document.addEventListener('item-selected-from-sidebar', event => {
                const { itemId, type } = event.detail;
                console.log(`Sidebar item clicked: ${itemId} (${type}). Forwarding to Livewire backend.`);
                Livewire.dispatch('itemSelected', { itemId, type });
            });

            Livewire.on('dataUpdated', (event) => {
                const map = window.mapInstance;
                // [PERBAIKAN] Mengakses `event.data` langsung, bukan `event[0].data`
                const data = event.data;
                if (!map || !map.isStyleLoaded() || !data) return;

                console.log('Menerima dataUpdated, menggambar ulang peta.');

                Object.values(window.mapMarkers).forEach(marker => marker.remove());
                window.mapMarkers = {};
                if (map.getLayer('routes-layer')) map.removeLayer('routes-layer');
                if (map.getSource('routes')) map.removeSource('routes');

                const routeFeatures = [];

                data.forEach(item => {
                    if (item.type === 'note' && item.latitude && item.longitude) {
                        const popupContent = `<b>${item.title || 'Tanpa Judul'}</b><p>${item.description || ''}</p>`;
                        const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);

                        const marker = new mapboxgl.Marker()
                            .setLngLat([item.longitude, item.latitude])
                            .setPopup(popup)
                            .addTo(map);

                        window.mapMarkers[item.id] = marker;
                    } else if (item.type === 'route' && Array.isArray(item.route) && item.route.length > 1) {
                        const coordinates = item.route.map(p => [p.longitude, p.latitude]);
                        routeFeatures.push({
                            'type': 'Feature',
                            'properties': {},
                            'geometry': { 'type': 'LineString', 'coordinates': coordinates }
                        });
                    }
                });

                if (routeFeatures.length > 0) {
                    map.addSource('routes', {
                        'type': 'geojson',
                        'data': { 'type': 'FeatureCollection', 'features': routeFeatures }
                    });
                    map.addLayer({
                        'id': 'routes-layer',
                        'type': 'line',
                        'source': 'routes',
                        'layout': { 'line-join': 'round', 'line-cap': 'round' },
                        'paint': { 'line-color': '#e01e5a', 'line-width': 5, 'line-opacity': 0.8 }
                    });
                }
            });

            Livewire.on('zoomToItem', (event) => {
                const map = window.mapInstance;
                // [PERBAIKAN] Mengakses `event.item` langsung, bukan `event[0].item`
                const item = event.item;
                if (!map || !item) return;

                console.log(`Menerima zoomToItem, zoom ke item ID: ${item.id}`);

                if (item.type === 'note' && item.latitude && item.longitude) {
                    map.flyTo({
                        center: [item.longitude, item.latitude],
                        zoom: 16,
                        essential: true
                    });
                    if(window.mapMarkers[item.id]) {
                        setTimeout(() => {
                           window.mapMarkers[item.id].togglePopup();
                        }, 500);
                    }
                } else if (item.type === 'route' && Array.isArray(item.route) && item.route.length > 0) {
                    const coordinates = item.route.map(p => [p.longitude, p.latitude]);
                    const bounds = coordinates.reduce(
                        (b, coord) => b.extend(coord),
                        new mapboxgl.LngLatBounds(coordinates[0], coordinates[0])
                    );
                    map.fitBounds(bounds, { padding: 60, maxZoom: 16 });
                }
            });

            Livewire.on('initialDataLoaded', (event) => {
                const map = window.mapInstance;
                // [PERBAIKAN] Mengakses `event.data` langsung, bukan `event[0].data`
                const data = event.data;
                if (!map || !data || data.length === 0) return;

                console.log('Menerima initialDataLoaded, menyesuaikan batas peta.');
                const bounds = new mapboxgl.LngLatBounds();
                let pointsAdded = 0;
                data.forEach(item => {
                    if (item.type === 'note' && item.latitude && item.longitude) {
                        bounds.extend([item.longitude, item.latitude]);
                        pointsAdded++;
                    } else if (item.type === 'route' && Array.isArray(item.route)) {
                        item.route.forEach(p => {
                            if (p.longitude && p.latitude) {
                                bounds.extend([p.longitude, p.latitude]);
                                pointsAdded++;
                            }
                        });
                    }
                });

                if (pointsAdded > 1) {
                    map.fitBounds(bounds, { padding: 50, duration: 1000 });
                } else if (pointsAdded === 1) {
                    map.flyTo({ center: bounds.getCenter(), zoom: 14, duration: 1000 });
                }
            });
        </script>
    </body>
</html>
