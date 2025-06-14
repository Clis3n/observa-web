<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\FirebaseLoginController;
use App\Livewire\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Sekarang, semua rute di file ini secara otomatis akan
| menggunakan grup middleware 'web' yang kita definisikan di bootstrap/app.php.
|
*/

// PERUBAHAN 1: Jadikan Landing Page sebagai Rute Utama (root)
Route::get('/', function () {
    return view('landing');
})->name('landing'); // Beri nama rute 'landing'

// PERUBAHAN 2: Pindahkan rute login ke '/login'
// Rute untuk halaman login
Route::get('/login', [FirebaseLoginController::class, 'showLoginForm'])->name('login');

// PERUBAHAN 3: Rute 'login.show' tidak diperlukan lagi karena sudah tercakup oleh rute di atas.
// Jika Anda masih membutuhkannya di tempat lain, Anda bisa biarkan, tapi biasanya duplikat.
// Route::get('/login', [FirebaseLoginController::class, 'showLoginForm'])->name('login.show'); // Baris ini bisa dihapus atau dikomentari


// Rute untuk memproses token dari frontend (TETAP SAMA)
Route::post('/login/firebase', [FirebaseLoginController::class, 'handleLogin'])->name('login.firebase');

// Rute untuk logout (TETAP SAMA)
Route::post('/logout', [FirebaseLoginController::class, 'handleLogout'])->name('logout');

// Grup rute yang dilindungi (TETAP SAMA)
Route::middleware(['firebase.auth'])->group(function () {
    // Halaman dashboard utama
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
