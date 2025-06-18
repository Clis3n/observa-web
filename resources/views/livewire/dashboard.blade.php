<div wire:poll.15s.keep-alive="@if(!$isEditMode) loadData @endif">
    <!-- Loading Overlay -->
    <div x-data x-show="$store.dashboardState.isLoading"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-50 flex flex-col items-center justify-center z-50">
        <div class="flex items-center space-x-4">
            <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-yellow-500"></div>
            <span class="text-xl font-semibold text-gray-700">Memuat Data dan Peta...</span>
        </div>
    </div>

    <div class="h-screen-minus-header flex bg-gray-100 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-full md:w-[380px] h-full flex flex-col bg-white border-r border-gray-200 flex-shrink-0">

            <!-- Header Sidebar -->
            <div class="p-4 border-b flex justify-between items-center flex-shrink-0">
                @if($isSelectionMode)
                    <h2 class="text-xl font-bold text-gray-800">{{ count($selectedIds) }} item dipilih</h2>
                @else
                    <h2 class="text-xl font-bold text-gray-800">Catatan Perjalanan</h2>
                @endif

                @if(!empty($combinedData))
                    <div>
                        @if($isSelectionMode)
                            <button wire:click="toggleSelectionMode" class="px-3 py-1 text-sm font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                                Batal
                            </button>
                        @else
                            <button wire:click="toggleSelectionMode" title="Pilih beberapa item untuk diekspor" class="px-3 py-1 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-md transition-colors">
                                <i class="fas fa-check-double mr-2"></i> Pilih
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Panel Seleksi dan Ekspor [DIRIMBAK] -->
            @if($isSelectionMode)
            <div class="flex-shrink-0 p-3 border-b bg-white" x-data="{ open: false }" x-transition>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="select-all-checkbox"
                            wire:click="selectAllItems"
                            @if($selectAll) checked @endif
                            class="h-5 w-5 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 cursor-pointer">
                        <label for="select-all-checkbox" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer" wire:click="selectAllItems">Pilih Semua</label>
                    </div>

                    @if(!empty($selectedIds))
                        <div class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none">
                                <i class="fas fa-file-export mr-2"></i>
                                Ekspor ({{ count($selectedIds) }})
                                <i class="fas fa-chevron-down ml-2 -mr-1 h-3 w-3"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20" style="display: none;">
                                <div class="py-1" role="menu">
                                    <span class="block px-4 py-2 text-xs text-gray-400 uppercase">Pilih Format</span>
                                    <button @click="document.getElementById('exportForm').action='{{ route('export.excel') }}'; document.getElementById('exportForm').submit(); open = false" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"><i class="fas fa-file-excel w-5 mr-3 text-green-600"></i><span>Excel (.xlsx)</span></button>
                                    <button @click="document.getElementById('exportForm').action='{{ route('export.kml') }}'; document.getElementById('exportForm').submit(); open = false" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"><i class="fas fa-globe-americas w-5 mr-3 text-blue-600"></i><span>Google Earth (.kml)</span></button>
                                    <button @click="document.getElementById('exportForm').action='{{ route('export.kml', ['format' => 'kmz']) }}'; document.getElementById('exportForm').submit(); open = false" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"><i class="fas fa-file-archive w-5 mr-3 text-purple-600"></i><span>Google Earth (.kmz)</span></button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Pesan Notifikasi -->
            <div class="p-2 flex-shrink-0">
                @if (session()->has('message'))
                    <div class="px-4 py-2 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm rounded-r-md" role="alert"><p>{{ session('message') }}</p></div>
                @endif
                @if (session()->has('error'))
                    <div class="px-4 py-2 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm rounded-r-md" role="alert"><p>{{ session('error') }}</p></div>
                @endif
            </div>

            <!-- Konten Sidebar (Bisa di-scroll) -->
            <div class="flex-grow overflow-y-auto">
                @if ($selectedItem && !$isSelectionMode)
                    <!-- Tampilan Detail atau Edit (Tidak berubah) -->
                    <div class="p-5" wire:key="item-view-{{ $selectedItem['id'] }}">
                        @if ($isEditMode)
                            <!-- TAMPILAN MODE EDIT SPASIAL -->
                            <div wire:key="edit-{{ $selectedItem['id'] }}">
                                <h3 class="text-xl font-bold text-gray-900">Edit Koordinat</h3>
                                <p class="text-sm text-gray-500 mb-4">Geser di peta atau ketik manual.</p>
                                @if ($selectedItem['type'] === 'note')
                                <div class="space-y-3">
                                    <div><label for="latitude" class="block text-xs font-medium text-gray-700">Latitude</label><input type="number" step="any" id="latitude" wire:model.live.debounce.500ms="selectedItem.latitude" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"></div>
                                    <div><label for="longitude" class="block text-xs font-medium text-gray-700">Longitude</label><input type="number" step="any" id="longitude" wire:model.live.debounce.500ms="selectedItem.longitude" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"></div>
                                </div>
                                @else
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700"><i class="fas fa-info-circle mr-2"></i>Untuk mengedit rute, silakan geser titik-titik (vertices) yang muncul pada peta.</div>
                                @endif
                                <div class="mt-6 flex space-x-3 border-t pt-5">
                                    <button wire:click="cancelEditMode" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                                    <button wire:click="saveSpatialChanges" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors"><i class="fas fa-save mr-2"></i> Simpan</button>
                                </div>
                            </div>
                        @else
                            <!-- TAMPILAN DETAIL NORMAL -->
                            <div wire:key="details-{{ $selectedItem['id'] }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 break-words">{{ $selectedItem['title'] ?: 'Tanpa Judul' }}</h3>
                                        <span @class(['inline-block text-xs font-semibold uppercase px-2.5 py-1 rounded-full mt-2', 'bg-yellow-100 text-yellow-800'])>{{ $selectedItem['type'] === 'note' ? 'Titik Lokasi' : 'Rute' }}</span>
                                    </div>
                                    <button wire:click="clearSelection" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                                </div>
                                <p class="mt-4 text-gray-600 text-sm break-words">{{ $selectedItem['description'] ?: 'Tidak ada deskripsi.' }}</p>
                                <div class="mt-5 text-sm text-gray-600 border-t pt-4 space-y-2">
                                    @if ($selectedItem['type'] === 'note' && isset($selectedItem['latitude']))<div class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-center mr-2 text-gray-400"></i><span>{{ number_format($selectedItem['latitude'], 5) }}, {{ number_format($selectedItem['longitude'], 5) }}</span></div>@endif
                                    @if ($selectedItem['type'] === 'route' && isset($selectedItem['route']))<div class="flex items-center"><i class="fas fa-route w-5 text-center mr-2 text-gray-400"></i><span>{{ count($selectedItem['route']) }} titik rute</span></div>@endif
                                    @if (isset($selectedItem['timestamp']))<div class="flex items-center"><i class="fas fa-calendar-alt w-5 text-center mr-2 text-gray-400"></i><span>{{ \Carbon\Carbon::parse($selectedItem['timestamp'])->translatedFormat('d F Y, H:i') }}</span></div>@endif
                                </div>
                                <div class="mt-6 flex space-x-3 border-t pt-5">
                                    <button wire:click="enterEditMode" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-gray-800 rounded-lg hover:bg-gray-700 transition-colors"><i class="fas fa-map-marked-alt mr-2"></i> Edit Peta</button>
                                    <button wire:click="editItem('{{ $selectedItem['id'] }}')" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors"><i class="fas fa-edit mr-2"></i> Edit Teks</button>
                                </div>
                                <div class="mt-3"><button wire:click="deleteItem('{{ $selectedItem['id'] }}', '{{ $selectedItem['type'] }}')" wire:confirm="Anda yakin ingin menghapus item ini?" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors"><i class="fas fa-trash mr-2"></i> Hapus Item Ini</button></div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Tampilan Daftar [DIRIMBAK] -->
                    <ul class="p-3 space-y-2">
                        @forelse ($combinedData as $item)
                            <li wire:key="list-item-{{ $item['id'] }}"
                                @class(['p-3 rounded-lg bg-white border transition-all duration-200 flex items-center',
                                        'border-yellow-400 bg-yellow-50' => in_array($item['type'].':'.$item['id'], $selectedIds),
                                        'border-gray-200' => !in_array($item['type'].':'.$item['id'], $selectedIds)])
                            >

                                @if($isSelectionMode)
                                    <input
                                        type="checkbox"
                                        wire:key="checkbox-{{ $item['id'] }}"
                                        wire:click="selectItemById('{{ $item['type'] }}:{{ $item['id'] }}')"
                                        @if(in_array($item['type'].':'.$item['id'], $selectedIds)) checked @endif
                                        class="h-5 w-5 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 flex-shrink-0 mr-4 cursor-pointer">
                                @endif

                                <div @if(!$isSelectionMode) @click="$dispatch('item-selected-from-sidebar', { itemId: '{{ $item['id'] }}' })" @endif
                                     @class(['flex-grow', 'cursor-pointer' => !$isSelectionMode, 'cursor-default' => $isSelectionMode])>
                                    <h3 class="font-semibold text-gray-800 truncate">{{ $item['title'] ?: 'Tanpa Judul' }}</h3>
                                    <div class="flex justify-between items-center text-xs text-gray-500 mt-1">
                                        <span class="flex items-center font-medium text-yellow-600">
                                            @if ($item['type'] === 'note')<i class="fas fa-map-marker-alt w-4 text-center mr-1.5"></i> Titik Lokasi @else<i class="fas fa-route w-4 text-center mr-1.5"></i> Rute @endif
                                        </span>
                                        @if (isset($item['timestamp']))<span>{{ \Carbon\Carbon::parse($item['timestamp'])->diffForHumans() }}</span>@endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <div class="text-center p-10 text-gray-500"><i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i><p class="font-semibold">Tidak Ada Data</p><p class="text-sm">Data perjalananmu akan muncul di sini.</p></div>
                        @endforelse
                    </ul>
                @endif
            </div>
        </aside>

        <!-- Container Peta -->
        <div wire:ignore class="hidden md:block flex-grow h-full"><div id='map' class="w-full h-full"></div></div>
    </div>

    <!-- Form tersembunyi untuk submit ekspor -->
    <form id="exportForm" method="POST" target="_blank" class="hidden">
        @csrf
        @foreach($selectedIds as $id)
            <input type="hidden" name="selected_ids[]" value="{{ $id }}">
        @endforeach
    </form>

    <!-- Modal Edit Teks (Tidak berubah) -->
    @if($showEditModal)
        <div x-data="{ show: @entangle('showEditModal').live }" x-show="show" x-on:keydown.escape.window="show = false" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 px-4" style="display: none;">
            <div @click.away="show = false" class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Item</h3>
                <form wire:submit.prevent="saveItem">
                    <div class="space-y-4">
                        <div><label for="title" class="block text-sm font-medium text-gray-700">Judul</label><input type="text" wire:model="editingItem.title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">@error('editingItem.title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                        <div><label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label><textarea wire:model="editingItem.description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"></textarea></div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="show = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold text-sm transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-semibold text-sm transition-colors">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>


@push('scripts')
{{-- Script Anda tidak perlu diubah sama sekali --}}
<script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('dashboardState')) {
            Alpine.store('dashboardState', {
                isLoading: true, mapIsReady: false, dataIsReady: false,
                checkIfDone() { if (this.mapIsReady && this.dataIsReady) this.isLoading = false; }
            });
        }
    });

    window.mapInstance = null;
    window.mapMarkers = {};
    window.drawInstance = null;
    let editableFeatureId = null;

    function createPopupContent(item) {
        const title = item.title || 'Tanpa Judul';
        const description = item.description || 'Tidak ada deskripsi.';
        const type_text = item.type === 'note' ? 'Titik Lokasi' : 'Rute';
        return `<div class="p-1"><h3 class="font-bold text-md text-gray-800">${title}</h3><span class="text-xs font-semibold uppercase px-2 py-0.5 rounded-full ${item.type === 'note' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800'}">${type_text}</span><p class="text-gray-600 text-sm mt-2">${description}</p></div>`;
    }

    function initializeMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement) return;
        if (typeof window.mapboxgl === 'undefined' || typeof window.MapboxDraw === 'undefined') {
            setTimeout(initializeMap, 100); return;
        }
        if (window.mapInstance) window.mapInstance.remove();
        mapboxgl.accessToken = 'pk.eyJ1IjoiY2xpc2VuYXJkeWxha3Nvbm93aWNha3Nvbm8iLCJhIjoiY20zc25wbjFnMGZ2eTJxc2ZhY2JkZjZ5ayJ9.MxI1flsCYndt7aXbImXMQw';
        window.mapInstance = new mapboxgl.Map({ container: 'map', style: 'mapbox://styles/mapbox/streets-v12', center: [110.3695, -7.7956], zoom: 9 });
        window.drawInstance = new MapboxDraw({ displayControlsDefault: false });
        window.mapInstance.addControl(window.drawInstance);
        window.mapInstance.on('load', () => {
            Alpine.store('dashboardState').mapIsReady = true;
            Alpine.store('dashboardState').checkIfDone();
            Livewire.dispatch('getInitialData');
            window.mapInstance.on('draw.update', (e) => {
                if (e.features.length > 0 && e.features[0].id === editableFeatureId) {
                    const updatedCoords = e.features[0].geometry.coordinates;
                    Livewire.dispatch('coordinatesUpdatedFromMap', { newCoords: updatedCoords });
                }
            });
        });
    }

    function setupEventListeners() {
        document.addEventListener('item-selected-from-sidebar', event => Livewire.dispatch('itemSelected', { ...event.detail }));
        Livewire.on('dataUpdated', ({ data }) => {
            const map = window.mapInstance; if (!map || !map.isStyleLoaded() || !data) return;
            Object.values(window.mapMarkers).forEach(marker => marker.remove());
            window.mapMarkers = {};
            if (map.getLayer('routes-layer')) map.removeLayer('routes-layer');
            if (map.getSource('routes')) map.removeSource('routes');
            const routeFeatures = [];
            data.forEach(item => {
                if (item.type === 'note' && item.latitude && item.longitude) {
                    const popup = new mapboxgl.Popup({ offset: 25, closeButton: false }).setHTML(createPopupContent(item));
                    const marker = new mapboxgl.Marker({ color: '#FBBC05' }).setLngLat([item.longitude, item.latitude]).setPopup(popup).addTo(map);
                    window.mapMarkers[item.id] = marker;
                } else if (item.type === 'route' && Array.isArray(item.route) && item.route.length > 1) {
                    const coordinates = item.route.map(p => [p.longitude, p.latitude]);
                    routeFeatures.push({ 'type': 'Feature', 'properties': {}, 'geometry': { 'type': 'LineString', 'coordinates': coordinates }});
                }
            });
            if (routeFeatures.length > 0) {
                map.addSource('routes', { 'type': 'geojson', 'data': { 'type': 'FeatureCollection', 'features': routeFeatures }});
                map.addLayer({ 'id': 'routes-layer', 'type': 'line', 'source': 'routes', 'layout': { 'line-join': 'round', 'line-cap': 'round' }, 'paint': { 'line-color': '#FBBC05', 'line-width': 4, 'line-opacity': 0.8 }});
            }
        });
        Livewire.on('startMapEditing', ({ item }) => {
            const map = window.mapInstance; const draw = window.drawInstance; if (!map || !draw || !item) return;
            Object.values(window.mapMarkers).forEach(marker => marker.remove());
            if (map.getLayer('routes-layer')) map.setLayoutProperty('routes-layer', 'visibility', 'none');
            if (item.type === 'note' && item.latitude && item.longitude) {
                const draggableMarker = new mapboxgl.Marker({ color: '#EAB308', draggable: true }).setLngLat([item.longitude, item.latitude]).addTo(map);
                draggableMarker.on('dragend', () => Livewire.dispatch('coordinatesUpdatedFromMap', { newCoords: draggableMarker.getLngLat() }));
                window.mapMarkers[item.id] = draggableMarker;
            } else if (item.type === 'route' && Array.isArray(item.route)) {
                const coordinates = item.route.map(p => [p.longitude, p.latitude]);
                editableFeatureId = draw.add({ id: item.id, type: 'Feature', properties: {}, geometry: { type: 'LineString', coordinates: coordinates }})[0];
                draw.changeMode('direct_select', { featureId: editableFeatureId });
            }
        });
        Livewire.on('stopMapEditing', ({ item }) => {
            const map = window.mapInstance; const draw = window.drawInstance; if (!map) return;
            if (item && window.mapMarkers[item.id]) { window.mapMarkers[item.id].remove(); delete window.mapMarkers[item.id]; }
            if (draw) draw.deleteAll();
            if (map.getLayer('routes-layer')) map.setLayoutProperty('routes-layer', 'visibility', 'visible');
            Livewire.dispatch('dataUpdated', { data: @json($this->combinedData) });
        });
        Livewire.on('itemCoordinatesUpdated', ({ item }) => {
            if (item && item.type === 'note' && window.mapMarkers[item.id] && window.mapMarkers[item.id].setLngLat) {
                window.mapMarkers[item.id].setLngLat([item.longitude, item.latitude]);
            }
        });
        Livewire.on('zoomToItem', ({ item }) => {
            const map = window.mapInstance;
            if (!map || !item) return;
            if (item.type === 'note' && item.latitude && item.longitude) {
                map.flyTo({ center: [item.longitude, item.latitude], zoom: 16, essential: true });
                if(window.mapMarkers[item.id]) { setTimeout(() => window.mapMarkers[item.id].togglePopup(), 500); }
            } else if (item.type === 'route' && Array.isArray(item.route) && item.route.length > 0) {
                const coordinates = item.route.map(p => [p.longitude, p.latitude]);
                const bounds = coordinates.reduce((b, coord) => b.extend(coord), new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                map.fitBounds(bounds, { padding: 60, maxZoom: 16, essential: true });
            }
        });
        Livewire.on('initialDataLoaded', ({ data }) => {
            Alpine.store('dashboardState').dataIsReady = true;
            Alpine.store('dashboardState').checkIfDone();
            const map = window.mapInstance; if (!map || !data || data.length === 0) return;
            const bounds = new mapboxgl.LngLatBounds(); let pointsAdded = 0;
            data.forEach(item => {
                if (item.type === 'note' && item.latitude && item.longitude) { bounds.extend([item.longitude, item.latitude]); pointsAdded++; }
                else if (item.type === 'route' && Array.isArray(item.route)) { item.route.forEach(p => { if (p.longitude && p.latitude) { bounds.extend([p.longitude, p.latitude]); pointsAdded++; }});}
            });
            if (pointsAdded > 1) { map.fitBounds(bounds, { padding: 50, duration: 1000, maxZoom: 15 }); }
            else if (pointsAdded === 1) { map.flyTo({ center: bounds.getCenter(), zoom: 14, duration: 1000 }); }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initializeMap();
        setupEventListeners();
    });
    document.addEventListener('livewire:navigated', () => {
        if (Alpine.store('dashboardState')) {
            Alpine.store('dashboardState').isLoading = true;
            Alpine.store('dashboardState').mapIsReady = false;
            Alpine.store('dashboardState').dataIsReady = false;
        }
        initializeMap();
    });
</script>
@endpush
