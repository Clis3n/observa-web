<div>
    <!-- Polling data secara berkala -->
    <div wire:poll.15s.keep-alive="loadData"></div>

    <div class="h-screen-minus-header flex bg-gray-100 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-full md:w-[380px] h-full flex flex-col bg-white border-r border-gray-200 flex-shrink-0">
            <!-- Header Sidebar -->
            <div class="p-4 border-b flex justify-between items-center flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-800">Catatan Perjalanan</h2>
            </div>

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
                @if ($selectedItem)
                    <!-- Tampilan Detail -->
                    <div class="p-5" wire:key="details-{{ $selectedItem['id'] }}">
                        <button wire:click="clearSelection" class="flex items-center text-sm font-semibold text-yellow-600 hover:text-yellow-500 mb-4 transition-colors"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar</button>
                        <h3 class="text-xl font-bold text-gray-900 break-words">{{ $selectedItem['title'] ?: 'Tanpa Judul' }}</h3>
                        <span @class(['inline-block text-xs font-semibold uppercase px-2.5 py-1 rounded-full mt-2', 'bg-yellow-100 text-yellow-800'])>{{ $selectedItem['type'] === 'note' ? 'Titik Lokasi' : 'Rute' }}</span>
                        <p class="mt-4 text-gray-600 text-sm break-words">{{ $selectedItem['description'] ?: 'Tidak ada deskripsi.' }}</p>
                        <div class="mt-5 text-sm text-gray-600 border-t pt-4 space-y-2">
                            @if ($selectedItem['type'] === 'note' && isset($selectedItem['latitude']))
                                <div class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-center mr-2 text-gray-400"></i><span>{{ number_format($selectedItem['latitude'], 5) }}, {{ number_format($selectedItem['longitude'], 5) }}</span></div>
                            @elseif ($selectedItem['type'] === 'route' && isset($selectedItem['route']))
                                <div class="flex items-center"><i class="fas fa-route w-5 text-center mr-2 text-gray-400"></i><span>{{ count($selectedItem['route']) }} titik rute</span></div>
                            @endif
                            <div class="flex items-center"><i class="fas fa-calendar-alt w-5 text-center mr-2 text-gray-400"></i><span>{{ \Carbon\Carbon::parse($selectedItem['timestamp'])->translatedFormat('d F Y, H:i') }}</span></div>
                        </div>
                        <div class="mt-6 flex space-x-3 border-t pt-5">
                            <!-- PERBAIKAN: Tombol Edit diganti warnanya -->
                            <button wire:click="editItem('{{ $selectedItem['id'] }}')" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors"><i class="fas fa-edit mr-2"></i> Edit</button>
                            <button wire:click="deleteItem('{{ $selectedItem['id'] }}', '{{ $selectedItem['type'] }}')" wire:confirm="Anda yakin ingin menghapus item ini?" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"><i class="fas fa-trash mr-2"></i> Hapus</button>
                        </div>
                    </div>
                @else
                    <!-- Tampilan Daftar -->
                    <ul class="p-3 space-y-2">
                        @forelse ($combinedData as $item)
                            <li wire:key="item-{{ $item['id'] }}" @click="$dispatch('item-selected-from-sidebar', { itemId: '{{ $item['id'] }}', type: '{{ $item['type'] }}' })" class="p-3 rounded-lg cursor-pointer hover:bg-yellow-50 bg-white border border-gray-200 hover:border-yellow-400 transition-all duration-200">
                                <h3 class="font-semibold text-gray-800 truncate">{{ $item['title'] ?: 'Tanpa Judul' }}</h3>
                                <div class="flex justify-between items-center text-xs text-gray-500 mt-1">
                                    <!-- PERBAIKAN: Warna teks keterangan item -->
                                    <span class="flex items-center font-medium text-yellow-600">
                                        @if ($item['type'] === 'note')
                                            <i class="fas fa-map-marker-alt w-4 text-center mr-1.5"></i> Titik Lokasi
                                        @else
                                            <i class="fas fa-route w-4 text-center mr-1.5"></i> Rute
                                        @endif
                                    </span>
                                    <span>{{ \Carbon\Carbon::parse($item['timestamp'])->diffForHumans() }}</span>
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
        <div wire:ignore class="hidden md:block flex-grow h-full">
            <div id='map' class="w-full h-full"></div>
        </div>
    </div>

    <!-- Modal Edit -->
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
                    <!-- PERBAIKAN: Tombol Simpan diganti warnanya -->
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-semibold text-sm transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>


@push('scripts')
<script>
    // Variabel global untuk menyimpan instance peta dan objek popup/marker
    window.mapInstance = null;
    window.mapMarkers = {};

    function createPopupContent(item) {
        const title = item.title || 'Tanpa Judul';
        const description = item.description || 'Tidak ada deskripsi.';
        const type_text = item.type === 'note' ? 'Titik Lokasi' : 'Rute';
        return `<div class="p-1"><h3 class="font-bold text-md text-gray-800">${title}</h3><span class="text-xs font-semibold uppercase px-2 py-0.5 rounded-full ${item.type === 'note' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}">${type_text}</span><p class="text-gray-600 text-sm mt-2">${description}</p></div>`;
    }

    // Fungsi Inisialisasi Peta
    function initializeMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement) return;

        if (window.mapInstance) {
            window.mapInstance.remove();
        }

        mapboxgl.accessToken = 'pk.eyJ1IjoiY2xpc2VuYXJkeWxha3Nvbm93aWNha3Nvbm8iLCJhIjoiY20zc25wbjFnMGZ2eTJxc2ZhY2JkZjZ5ayJ9.MxI1flsCYndt7aXbImXMQw';

        window.mapInstance = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [110.3695, -7.7956],
            zoom: 9
        });

        window.mapInstance.on('load', () => {
            console.log('Peta diinisialisasi.');
            Livewire.dispatch('getInitialData');
        });
    }

    // Inisialisasi peta saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', initializeMap);

    // [PERBAIKAN KUNCI] Inisialisasi ulang peta setiap kali Livewire melakukan navigasi
    document.addEventListener('livewire:navigated', initializeMap);

    document.addEventListener('item-selected-from-sidebar', event => {
        const { itemId, type } = event.detail;
        Livewire.dispatch('itemSelected', { itemId, type });
    });

    Livewire.on('dataUpdated', ({ data }) => {
        const map = window.mapInstance;
        if (!map || !map.isStyleLoaded() || !data) return;

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
            }
            else if (item.type === 'route' && Array.isArray(item.route) && item.route.length > 1) {
                const coordinates = item.route.map(p => [p.longitude, p.latitude]);
                routeFeatures.push({
                    'type': 'Feature',
                    'properties': {},
                    'geometry': { 'type': 'LineString', 'coordinates': coordinates }
                });
            }
        });

        if (routeFeatures.length > 0) {
            map.addSource('routes', { 'type': 'geojson', 'data': { 'type': 'FeatureCollection', 'features': routeFeatures }});
            map.addLayer({ 'id': 'routes-layer', 'type': 'line', 'source': 'routes', 'layout': { 'line-join': 'round', 'line-cap': 'round' }, 'paint': { 'line-color': '#FBBC05', 'line-width': 4, 'line-opacity': 0.8 }});
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
        const map = window.mapInstance;
        if (!map || !data || data.length === 0) return;

        const bounds = new mapboxgl.LngLatBounds();
        let pointsAdded = 0;

        data.forEach(item => {
            if (item.type === 'note' && item.latitude && item.longitude) {
                bounds.extend([item.longitude, item.latitude]);
                pointsAdded++;
            } else if (item.type === 'route' && Array.isArray(item.route)) {
                item.route.forEach(p => {
                    if (p.longitude && p.latitude) { bounds.extend([p.longitude, p.latitude]); pointsAdded++; }
                });
            }
        });
        if (pointsAdded > 1) {
            map.fitBounds(bounds, { padding: 50, duration: 1000, maxZoom: 15 });
        } else if (pointsAdded === 1) {
            map.flyTo({ center: bounds.getCenter(), zoom: 14, duration: 1000 });
        }
    });
</script>
@endpush
