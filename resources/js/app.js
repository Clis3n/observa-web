import './bootstrap';  // [BARU] Impor library Mapbox dan MapboxDraw
import mapboxgl from 'mapbox-gl';
import MapboxDraw from '@mapbox/mapbox-gl-draw';

// [BARU] Impor CSS yang diperlukan
import 'mapbox-gl/dist/mapbox-gl.css';
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';

// [BARU] Jadikan variabel tersedia secara global agar bisa diakses dari Blade
window.mapboxgl = mapboxgl;
window.MapboxDraw = MapboxDraw;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

