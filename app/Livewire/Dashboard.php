<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Services\FirebaseService; // Pastikan Anda punya service ini
use Illuminate\Support\Facades\Session;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $notes = [];
    public $routes = [];
    public $combinedData = [];

    // Properti ini kembali untuk mengontrol UI sidebar secara reaktif
    public ?array $selectedItem = null;

    // Properti ini untuk mengontrol modal edit
    public bool $showEditModal = false;
    public array $editingItem = []; // Data item yang sedang diedit di modal

    protected $firebaseService;

    public function boot(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function mount()
    {
        // Data tidak dimuat di mount, tapi menunggu event dari JS
    }

    #[On('getInitialData')]
    public function getInitialData()
    {
        $this->loadData();
        // Kirim event khusus untuk zoom awal
        $this->dispatch('initialDataLoaded', data: $this->combinedData);
    }

    public function loadData()
    {
        $userId = Session::get('firebase_user_id');
        if (!$userId) return;

        $this->notes = $this->firebaseService->getNotes($userId);
        $this->routes = $this->firebaseService->getRoutes($userId);
        $this->combinedData = array_merge($this->notes, $this->routes);

        // Urutkan berdasarkan timestamp, yang terbaru di atas
        usort($this->combinedData, function ($a, $b) {
            $timestampA = $a['timestamp'] ?? '1970-01-01T00:00:00Z';
            $timestampB = $b['timestamp'] ?? '1970-01-01T00:00:00Z';
            return strcmp($timestampB, $timestampA);
        });

        // Selalu kirim update ke peta setiap kali data dimuat ulang
        $this->dispatch('dataUpdated', data: $this->combinedData);
    }

    #[On('itemSelected')]
    public function selectItem($itemId, $type)
    {
        $data = collect($this->combinedData)->firstWhere('id', $itemId);

        if ($data) {
            // 1. Update properti untuk memicu re-render sidebar dan menampilkan detail
            $this->selectedItem = $data;

            // 2. Kirim event ke JavaScript untuk melakukan zoom pada peta
            $this->dispatch('zoomToItem', item: $data);
        }
    }

    public function clearSelection()
    {
        $this->selectedItem = null;
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
        $this->validate([
            'editingItem.title' => 'required|string|max:255',
            'editingItem.description' => 'nullable|string',
        ]);

        $userId = Session::get('firebase_user_id');
        if (!isset($this->editingItem['id'])) return;

        // Siapkan data untuk update
        $updateData = [
            'title' => $this->editingItem['title'],
            'description' => $this->editingItem['description'],
        ];

        if ($this->editingItem['type'] === 'note') {
            $this->firebaseService->updateNote($userId, $this->editingItem['id'], $updateData);
        } else {
            $this->firebaseService->updateRoute($userId, $this->editingItem['id'], $updateData);
        }

        $this->showEditModal = false;
        $this->selectedItem = null; // Hapus seleksi agar list di-refresh
        $this->loadData();
        session()->flash('message', 'Data berhasil diperbarui.');
    }

    public function deleteItem($itemId, $type)
    {
        if (config('app.env') === 'demo') {
            session()->flash('error', 'Fungsi hapus dinonaktifkan dalam mode demo.');
            return;
        }

        $userId = Session::get('firebase_user_id');
        $this->firebaseService->deleteItem($userId, $type, $itemId);

        $this->selectedItem = null; // Reset selectedItem setelah dihapus
        $this->loadData();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
