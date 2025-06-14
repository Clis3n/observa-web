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

// Rute untuk halaman login
Route::get('/', [FirebaseLoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [FirebaseLoginController::class, 'showLoginForm'])->name('login.show');

// Rute untuk memproses token dari frontend
Route::post('/login/firebase', [FirebaseLoginController::class, 'handleLogin'])->name('login.firebase');

// Rute untuk logout
Route::post('/logout', [FirebaseLoginController::class, 'handleLogout'])->name('logout');

// Grup rute yang dilindungi.
Route::middleware(['firebase.auth'])->group(function () {
    // Halaman dashboard utama
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
