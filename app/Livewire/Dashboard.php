<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    // Properti Data & UI
    public $notes = [];
    public $routes = [];
    public $combinedData = [];
    public ?array $selectedItem = null;
    public bool $isEditMode = false;
    public ?array $originalItemState = null;
    public bool $showEditModal = false;
    public array $editingItem = ['id' => '', 'type' => '', 'title' => '', 'description' => ''];

    // Properti untuk mode seleksi (State)
    public bool $isSelectionMode = false;
    public array $selectedIds = [];
    public bool $selectAll = false;

    protected $firebaseService;

    public function boot(FirebaseService $firebaseService) { $this->firebaseService = $firebaseService; }
    public function mount() { $this->clearSelection(); }

    #[On('getInitialData')]
    public function getInitialData()
    {
        $this->loadData();
        $this->dispatch('initialDataLoaded', data: $this->combinedData);
    }

    #[On('refresh-data')]
    public function loadData()
    {
        if ($this->isEditMode) return;
        $userId = Session::get('firebase_user_id');
        if (!$userId) return;
        $this->notes = $this->firebaseService->getNotes($userId);
        $this->routes = $this->firebaseService->getRoutes($userId);
        $this->combinedData = array_merge($this->notes, $this->routes);
        usort($this->combinedData, fn($a, $b) => strcmp($b['timestamp'] ?? '1970', $a['timestamp'] ?? '1970'));
        $this->dispatch('dataUpdated', data: $this->combinedData);
    }

    // =================================================================
    // [LOGIKA SELEKSI BARU - ROMBAK TOTAL]
    // =================================================================

    public function toggleSelectionMode()
    {
        $this->isSelectionMode = !$this->isSelectionMode;
        // Reset state jika keluar dari mode seleksi
        if (!$this->isSelectionMode) {
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    /**
     * Method ini dipanggil saat checkbox "Pilih Semua" diklik.
     */
    public function selectAllItems()
    {
        // Toggle status selectAll
        $this->selectAll = !$this->selectAll;

        // Jika sekarang harus memilih semua, isi array. Jika tidak, kosongkan.
        if ($this->selectAll) {
            $this->selectedIds = collect($this->combinedData)->map(fn($item) => $item['type'] . ':' . $item['id'])->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    /**
     * Method ini dipanggil saat checkbox per item diklik.
     */
    public function selectItemById($typedId)
    {
        // Jika ID sudah ada di array, hapus.
        if (in_array($typedId, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$typedId]));
        }
        // Jika belum ada, tambahkan.
        else {
            $this->selectedIds[] = $typedId;
        }

        // Sinkronkan kembali status checkbox "Pilih Semua" setelah ada perubahan.
        $this->syncSelectAllState();
    }

    /**
     * Helper untuk menyinkronkan properti $selectAll.
     */
    private function syncSelectAllState()
    {
        $totalItems = count($this->combinedData);
        if ($totalItems > 0) {
            $this->selectAll = count($this->selectedIds) === $totalItems;
        } else {
            $this->selectAll = false;
        }
    }

    // =================================================================
    // AKHIR DARI LOGIKA SELEKSI BARU
    // =================================================================

    // Method lainnya tidak berubah
    #[On('item-selected-from-sidebar')]
    public function selectItem($itemId) { if ($this->isSelectionMode) return; if ($this->isEditMode) $this->cancelEditMode(); $data = collect($this->combinedData)->firstWhere('id', $itemId); if ($data) { $this->selectedItem = $data; $this->dispatch('zoomToItem', item: $data); } }
    public function clearSelection() { if($this->isEditMode) $this->cancelEditMode(); $this->selectedItem = null; }
    public function enterEditMode() { if (!$this->selectedItem) return; $this->isEditMode = true; $this->originalItemState = $this->selectedItem; $this->dispatch('startMapEditing', item: $this->selectedItem); }
    public function cancelEditMode() { if (!$this->originalItemState) return; $this->isEditMode = false; $this->selectedItem = $this->originalItemState; $this->originalItemState = null; $this->dispatch('stopMapEditing', item: $this->selectedItem); }
    #[On('coordinatesUpdatedFromMap')]
    public function updateCoordinatesFromMap(array $newCoords) { if (!$this->isEditMode || !$this->selectedItem) return; if ($this->selectedItem['type'] === 'note') { $this->selectedItem['longitude'] = $newCoords['lng']; $this->selectedItem['latitude'] = $newCoords['lat']; } else { $this->selectedItem['route'] = collect($newCoords)->map(fn($coord) => ['longitude' => $coord[0], 'latitude' => $coord[1]])->all(); } }
    public function updatedSelectedItem($value, $key) { if ($this->isEditMode && (str_starts_with($key, 'latitude') || str_starts_with($key, 'longitude'))) { $this->dispatch('itemCoordinatesUpdated', item: $this->selectedItem); } }
    public function saveSpatialChanges() { if (!$this->isEditMode || !$this->selectedItem) return; $userId = Session::get('firebase_user_id'); $updateData = $this->selectedItem; if ($this->selectedItem['type'] === 'note') { $this->firebaseService->updateNote($userId, $this->selectedItem['id'], $updateData); } else { $this->firebaseService->updateRoute($userId, $this->selectedItem['id'], $updateData); } session()->flash('message', 'Koordinat berhasil diperbarui.'); $this->isEditMode = false; $this->originalItemState = null; $this->dispatch('stopMapEditing'); $this->loadData(); }
    public function editItem($itemId) { $itemToEdit = collect($this->combinedData)->firstWhere('id', $itemId); if ($itemToEdit) { $this->editingItem = $itemToEdit; $this->showEditModal = true; } }
    public function saveItem() { $userId = Session::get('firebase_user_id'); $updateData = $this->editingItem; if ($this->editingItem['type'] === 'note') { $this->firebaseService->updateNote($userId, $this->editingItem['id'], $updateData); } else { $this->firebaseService->updateRoute($userId, $this->editingItem['id'], $updateData); } $this->showEditModal = false; $this->selectedItem = null; $this->loadData(); session()->flash('message', 'Data berhasil diperbarui.'); }
    public function deleteItem($itemId, $type) { if (config('app.env') === 'demo') { session()->flash('error', 'Fungsi hapus dinonaktifkan dalam mode demo.'); return; } $userId = Session::get('firebase_user_id'); $this->firebaseService->deleteItem($userId, $type, $itemId); $typedId = $type . ':' . $itemId; $this->selectedIds = array_diff($this->selectedIds, [$typedId]); $this->syncSelectAllState(); $this->selectedItem = null; $this->loadData(); session()->flash('message', 'Data berhasil dihapus.'); }
    public function render() { return view('livewire.dashboard'); }
}
