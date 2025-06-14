<div>
    <!-- Div ini hanya bertugas untuk polling data secara berkala -->
    <div wire:poll.15s.keep-alive="loadData"></div>

    <div class="h-screen-minus-header flex bg-gray-50">

        <!-- Sidebar -->
        <div class="w-full md:w-1/3 h-full overflow-y-auto bg-white border-r border-gray-200">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold">Data Perjalanan</h2>
            </div>

            <!-- Pesan Notifikasi -->
            @if (session()->has('message'))
                <div class="p-4 m-4 bg-green-100 border border-green-400 text-green-700 rounded" role="alert">
                    {{ session('message') }}
                </div>
            @endif
             @if (session()->has('error'))
                <div class="p-4 m-4 bg-red-100 border border-red-400 text-red-700 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif


            <!-- Tampilan Detail Item Terpilih -->
            @if ($selectedItem)
                <div class="p-4" wire:key="details-{{ $selectedItem['id'] }}">
                    <button wire:click="clearSelection" class="text-sm text-blue-600 hover:underline mb-4">← Kembali ke Daftar</button>

                    <h3 class="text-lg font-bold text-gray-800">{{ $selectedItem['title'] ?: 'Tanpa Judul' }}</h3>
                    <span class="text-xs font-semibold uppercase px-2 py-1 rounded-full
                        {{ $selectedItem['type'] === 'note' ? 'bg-blue-200 text-blue-800' : 'bg-purple-200 text-purple-800' }}">
                        {{ $selectedItem['type'] === 'note' ? 'Titik Lokasi' : 'Rute' }}
                    </span>

                    <p class="mt-3 text-gray-600">{{ $selectedItem['description'] ?: 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-4 text-sm text-gray-500 border-t pt-3">
                        @if ($selectedItem['type'] === 'note')
                            <strong>Koordinat:</strong> {{ number_format($selectedItem['latitude'], 5) }}, {{ number_format($selectedItem['longitude'], 5) }}
                        @else
                            <strong>Jumlah Titik:</strong> {{ count($selectedItem['route']) }} titik
                        @endif
                        <br>
                        <strong>Dibuat:</strong> {{ \Carbon\Carbon::parse($selectedItem['timestamp'])->translatedFormat('d F Y, H:i') }}
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button wire:click="editItem('{{ $selectedItem['id'] }}')" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Edit
                        </button>
                        <button wire:click="deleteItem('{{ $selectedItem['id'] }}', '{{ $selectedItem['type'] }}')" wire:confirm="Anda yakin ingin menghapus item ini?" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            @else
            <!-- Tampilan Daftar Item -->
                <ul class="p-2">
                    @forelse ($combinedData as $item)
                        <li wire:key="item-{{ $item['id'] }}"
                            @click="$dispatch('item-selected-from-sidebar', { itemId: '{{ $item['id'] }}', type: '{{ $item['type'] }}' })"
                            class="p-3 mb-2 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors border">
                            <h3 class="font-semibold text-gray-800">{{ $item['title'] ?: 'Tanpa Judul' }}</h3>
                            <div class="flex justify-between items-center text-sm text-gray-500 mt-1">
                                <span>
                                    @if ($item['type'] === 'note')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                        Titik Lokasi
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" /></svg>
                                        Rute
                                    @endif
                                </span>
                                <span>{{ \Carbon\Carbon::parse($item['timestamp'])->diffForHumans() }}</span>
                            </div>
                        </li>
                    @empty
                        <p class="p-4 text-gray-500">Tidak ada data ditemukan.</p>
                    @endforelse
                </ul>
            @endif
        </div>

        <!-- Container Peta (diabaikan oleh Livewire) -->
        <div wire:ignore class="hidden md:block w-2/3 h-full">
            <div id='map' class="w-full h-full"></div>
        </div>
    </div>

    <!-- Modal Edit (Menggunakan AlpineJS untuk state show/hide) -->
    @if($showEditModal)
    <div x-data="{ show: @entangle('showEditModal') }"
         x-show="show"
         x-on:keydown.escape.window="show = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50"
         style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Item</h3>
            <form wire:submit.prevent="saveItem" class="mt-4">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" wire:model.defer="editingItem.title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('editingItem.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea wire:model.defer="editingItem.description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="show = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
