<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth; // <-- Import kelas Auth

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
     public function register(): void
    {
        // Daftarkan 'Firebase\Factory' sebagai singleton
        $this->app->singleton(Factory::class, function ($app) {
            // [VERIFIKASI] Pastikan ini menggunakan config services dan storage_path
            return (new Factory)
                ->withServiceAccount(config('services.firebase.credentials_path'));
        });

        // Daftarkan 'Firebase\Auth' secara eksplisit
        $this->app->singleton(Auth::class, function ($app) {
            return $app->make(Factory::class)->createAuth();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
