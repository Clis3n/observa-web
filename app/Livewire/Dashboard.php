<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon; // Pastikan Carbon diimpor

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $notes = [];
    public $routes = [];
    public $combinedData = [];
    public ?array $selectedItem = null;
    public bool $showEditModal = false;
    public array $editingItem = [
        'id' => '',
        'type' => '',
        'title' => '',
        'description' => ''
    ]; // Inisialisasi dengan struktur default

    protected $firebaseService;

    public function boot(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function mount()
    {
        // PERBAIKAN: Inisialisasi variabel di sini untuk memastikan selalu ada
        $this->selectedItem = null;
        $this->showEditModal = false;
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
            $this->selectedItem = $data;
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
            // Pastikan semua field yang dibutuhkan oleh form ada
            $this->editingItem = [
                'id' => $itemToEdit['id'],
                'type' => $itemToEdit['type'],
                'title' => $itemToEdit['title'] ?? '',
                'description' => $itemToEdit['description'] ?? '',
            ];
            $this->showEditModal = true;
        }
    }

    public function saveItem()
    {
        $this->validate([
            'editingItem.title' => 'nullable|string|max:255',
            'editingItem.description' => 'nullable|string',
        ]);

        $userId = Session::get('firebase_user_id');
        if (!isset($this->editingItem['id']) || empty($this->editingItem['id'])) {
            session()->flash('error', 'Gagal menyimpan, ID item tidak ditemukan.');
            return;
        }

        // Buat judul default jika kosong
        $title = !empty(trim($this->editingItem['title'])) ? $this->editingItem['title'] : null;

        $updateData = [
            'title' => $title,
            'description' => $this->editingItem['description'],
        ];

        // Hapus nilai null agar tidak menimpa data yang ada di Firebase dengan null
        $updateData = array_filter($updateData, function($value) {
            return !is_null($value);
        });

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
            session()->flash('error', 'Fungsi hapus dinonaktifkan dalam mode demo.');
            return;
        }

        $userId = Session::get('firebase_user_id');
        $this->firebaseService->deleteItem($userId, $type, $itemId);

        $this->selectedItem = null;
        $this->loadData();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
