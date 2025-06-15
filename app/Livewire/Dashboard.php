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
    public $notes = [];
    public $routes = [];
    public $combinedData = [];
    public ?array $selectedItem = null;
    public bool $isEditMode = false;
    public ?array $originalItemState = null;
    public bool $showEditModal = false;
    public array $editingItem = [
        'id' => '', 'type' => '', 'title' => '', 'description' => ''
    ];

    protected $firebaseService;

    public function boot(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function mount()
    {
        $this->clearSelection();
    }

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

    #[On('item-selected-from-sidebar')]
    public function selectItem($itemId)
    {
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

    public function enterEditMode()
    {
        if (!$this->selectedItem) return;
        $this->isEditMode = true;
        $this->originalItemState = $this->selectedItem;
        $this->dispatch('startMapEditing', item: $this->selectedItem);
    }

    public function cancelEditMode()
    {
        if (!$this->originalItemState) return;
        $this->isEditMode = false;
        $this->selectedItem = $this->originalItemState;
        $this->originalItemState = null;
        $this->dispatch('stopMapEditing', item: $this->selectedItem);
    }

    #[On('coordinatesUpdatedFromMap')]
    public function updateCoordinatesFromMap(array $newCoords)
    {
        if (!$this->isEditMode || !$this->selectedItem) return;
        if ($this->selectedItem['type'] === 'note') {
            $this->selectedItem['longitude'] = $newCoords['lng'];
            $this->selectedItem['latitude'] = $newCoords['lat'];
        } else {
            $this->selectedItem['route'] = collect($newCoords)->map(fn($coord) => ['longitude' => $coord[0], 'latitude' => $coord[1]])->all();
        }
    }

    public function updatedSelectedItem($value, $key)
    {
        if ($this->isEditMode && (str_starts_with($key, 'latitude') || str_starts_with($key, 'longitude'))) {
            $this->dispatch('itemCoordinatesUpdated', item: $this->selectedItem);
        }
    }

    public function saveSpatialChanges()
    {
        if (!$this->isEditMode || !$this->selectedItem) return;

        $userId = Session::get('firebase_user_id');
        $itemId = $this->selectedItem['id'];
        $type = $this->selectedItem['type'];

        // [PERBAIKAN] Ambil seluruh data dari item yang sedang diedit sebagai dasar
        $updateData = $this->selectedItem;

        if ($type === 'note') {
            if (!is_numeric($this->selectedItem['latitude']) || !is_numeric($this->selectedItem['longitude'])) {
                session()->flash('error', 'Latitude dan Longitude harus berupa angka.'); return;
            }
            // Timpa hanya data spasial dengan nilai baru
            $updateData['latitude'] = (float)$this->selectedItem['latitude'];
            $updateData['longitude'] = (float)$this->selectedItem['longitude'];

            // Perbarui juga field 'place' agar konsisten
            $updateData['place'] = $updateData['latitude'] . ", " . $updateData['longitude'];

            $this->firebaseService->updateNote($userId, $itemId, $updateData);
        } else {
            // Timpa hanya data spasial dengan nilai baru
            $updateData['route'] = $this->selectedItem['route'];
            $this->firebaseService->updateRoute($userId, $itemId, $updateData);
        }

        session()->flash('message', 'Koordinat berhasil diperbarui.');
        $this->isEditMode = false;
        $this->originalItemState = null;
        $this->dispatch('stopMapEditing');
        $this->loadData();
    }

    public function editItem($itemId)
    {
        $itemToEdit = collect($this->combinedData)->firstWhere('id', $itemId);
        if ($itemToEdit) {
            $this->editingItem = $itemToEdit;
            $this->showEditModal = true;
        }
    }

    public function saveItem()
    {
        $this->validate(['editingItem.title' => 'nullable|string|max:255', 'editingItem.description' => 'nullable|string']);
        $userId = Session::get('firebase_user_id');
        if (!isset($this->editingItem['id']) || empty($this->editingItem['id'])) {
            session()->flash('error', 'Gagal menyimpan, ID item tidak ditemukan.'); return;
        }

        $title = !empty(trim($this->editingItem['title'])) ? $this->editingItem['title'] : 'Tanpa Judul';

        $updateData = $this->editingItem;
        $updateData['title'] = $title;
        $updateData['description'] = $this->editingItem['description'];

        unset($updateData['id']);
        unset($updateData['type']);

        if ($this->editingItem['type'] === 'note') {
            $this->firebaseService->updateNote($userId, $this->editingItem['id'], $updateData);
        } else {
            $this->firebaseService->updateRoute($userId, $this->editingItem['id'], $updateData);
        }

        $this->showEditModal = false;
        $this->selectedItem = null;
        $this->loadData();
        session()->flash('message', 'Data berhasil diperbarui.');
    }

    public function deleteItem($itemId, $type)
    {
        if (config('app.env') === 'demo') {
            session()->flash('error', 'Fungsi hapus dinonaktifkan dalam mode demo.'); return;
        }
        $userId = Session::get('firebase_user_id');
        $this->firebaseService->deleteItem($userId, $type, $itemId);
        $this->selectedItem = null; $this->loadData();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
