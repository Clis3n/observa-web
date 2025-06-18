<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
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

        // Hanya muat data jika kosong untuk mencegah reset saat seleksi
        if (empty($this->combinedData)) {
            $this->notes = $this->firebaseService->getNotes($userId);
            $this->routes = $this->firebaseService->getRoutes($userId);
            $this->combinedData = array_merge($this->notes, $this->routes);
            usort($this->combinedData, fn($a, $b) => strcmp($b['timestamp'] ?? '1970', $a['timestamp'] ?? '1970'));
        }

        $this->dispatch('dataUpdated', data: $this->combinedData);
    }

    // =================================================================
    // LOGIKA KLIK DAN SELEKSI
    // =================================================================

    /**
     * Method "pintar" yang menangani semua klik pada item daftar.
     * Ia akan memutuskan tindakan berdasarkan $isSelectionMode.
     */
    public function handleItemClick($itemId, $itemType)
    {
        if ($this->isSelectionMode) {
            // Jika dalam mode seleksi, panggil logika untuk memilih/membatalkan pilihan.
            $typedId = $itemType . ':' . $itemId;
            $this->selectItemById($typedId);
        } else {
            // Jika dalam mode normal, panggil logika untuk menampilkan detail.
            $this->selectItem($itemId);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan mode seleksi.
     */
    public function toggleSelectionMode()
    {
        $this->isSelectionMode = !$this->isSelectionMode;
        if (!$this->isSelectionMode) {
            $this->selectedIds = [];
        } else {
            // Saat masuk mode seleksi, pastikan tidak ada item detail yang ditampilkan
            $this->clearSelection();
        }
    }

    /**
     * Menambah atau menghapus satu item dari daftar yang dipilih.
     */
    public function selectItemById($typedId)
    {
        if (in_array($typedId, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$typedId]));
        } else {
            $this->selectedIds[] = $typedId;
        }
    }

    /**
     * Computed Property untuk mengecek apakah semua item sudah terpilih.
     */
    #[Computed]
    public function areAllItemsSelected(): bool
    {
        if (empty($this->combinedData) || !$this->isSelectionMode) {
            return false;
        }
        return count($this->selectedIds) === count($this->combinedData);
    }

    /**
     * Menangani klik pada tombol "Pilih Semua / Batal Pilih Semua".
     */
    public function toggleSelectAll()
    {
        if ($this->areAllItemsSelected()) {
            $this->unselectAllItems();
        } else {
            $this->selectAllItems();
        }
    }

    /**
     * Helper untuk memilih semua item.
     */
    public function selectAllItems()
    {
        $this->selectedIds = collect($this->combinedData)->map(function ($item) {
            return $item['type'] . ':' . $item['id'];
        })->all();
    }

    /**
     * Helper untuk membatalkan pilihan semua item.
     */
    public function unselectAllItems()
    {
        $this->selectedIds = [];
    }

    // =================================================================
    // LOGIKA TAMPILAN DETAIL & EDIT
    // =================================================================

    /**
     * Menampilkan detail item di sidebar dan peta.
     * Tidak lagi memiliki listener @On, dipanggil langsung oleh handleItemClick.
     */
    public function selectItem($itemId)
    {
        if ($this->isSelectionMode) return;
        if ($this->isEditMode) $this->cancelEditMode();
        $data = collect($this->combinedData)->firstWhere('id', $itemId);
        if ($data) {
            $this->selectedItem = $data;
            $this->dispatch('zoomToItem', item: $data);
        }
    }

    public function clearSelection()
    {
        if($this->isEditMode) $this->cancelEditMode();
        $this->selectedItem = null;
    }

    public function enterEditMode() { if (!$this->selectedItem) return; $this->isEditMode = true; $this->originalItemState = $this->selectedItem; $this->dispatch('startMapEditing', item: $this->selectedItem); }
    public function cancelEditMode() { if (!$this->originalItemState) return; $this->isEditMode = false; $this->selectedItem = $this->originalItemState; $this->originalItemState = null; $this->dispatch('stopMapEditing', item: $this->selectedItem); }
    #[On('coordinatesUpdatedFromMap')]
    public function updateCoordinatesFromMap(array $newCoords) { if (!$this->isEditMode || !$this->selectedItem) return; if ($this->selectedItem['type'] === 'note') { $this->selectedItem['longitude'] = $newCoords['lng']; $this->selectedItem['latitude'] = $newCoords['lat']; } else { $this->selectedItem['route'] = collect($newCoords)->map(fn($coord) => ['longitude' => $coord[0], 'latitude' => $coord[1]])->all(); } }
    public function updatedSelectedItem($value, $key) { if ($this->isEditMode && (str_starts_with($key, 'latitude') || str_starts_with($key, 'longitude'))) { $this->dispatch('itemCoordinatesUpdated', item: $this->selectedItem); } }
    public function saveSpatialChanges() { if (!$this->isEditMode || !$this->selectedItem) return; $userId = Session::get('firebase_user_id'); $updateData = $this->selectedItem; if ($this->selectedItem['type'] === 'note') { $this->firebaseService->updateNote($userId, $this->selectedItem['id'], $updateData); } else { $this->firebaseService->updateRoute($userId, $this->selectedItem['id'], $updateData); } session()->flash('message', 'Koordinat berhasil diperbarui.'); $this->isEditMode = false; $this->originalItemState = null; $this->dispatch('stopMapEditing'); $this->loadData(); }
    public function editItem($itemId) { $itemToEdit = collect($this->combinedData)->firstWhere('id', $itemId); if ($itemToEdit) { $this->editingItem = $itemToEdit; $this->showEditModal = true; } }
    public function saveItem() { $userId = Session::get('firebase_user_id'); $updateData = $this->editingItem; if ($this->editingItem['type'] === 'note') { $this->firebaseService->updateNote($userId, $this->editingItem['id'], $updateData); } else { $this->firebaseService->updateRoute($userId, $this->editingItem['id'], $updateData); } $this->showEditModal = false; $this->selectedItem = null; $this->loadData(); session()->flash('message', 'Data berhasil diperbarui.'); }
    public function deleteItem($itemId, $type) { if (config('app.env') === 'demo') { session()->flash('error', 'Fungsi hapus dinonaktifkan dalam mode demo.'); return; } $userId = Session::get('firebase_user_id'); $this->firebaseService->deleteItem($userId, $type, $itemId); $typedId = $type . ':' . $itemId; $this->selectedIds = array_diff($this->selectedIds, [$typedId]); $this->selectedItem = null; $this->loadData(); session()->flash('message', 'Data berhasil dihapus.'); }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
