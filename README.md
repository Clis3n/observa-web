<p align="center">
  <img src="https://raw.githubusercontent.com/Clis3n/observa-web/main/public/landing_assets/image/icon.svg" width="100" alt="Observa Logo">
</p>

<h1 align="center">Observa Web</h1>

<p align="center">
  <strong>Versi Web dari Aplikasi Android Observa</strong>
</p>

<p align="center">
  <!-- Badges/Lencana yang Akurat -->
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Framework-Laravel_11-FF2D20.svg?style=for-the-badge&logo=laravel" alt="Built with Laravel"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3-4d55d9.svg?style=for-the-badge&logo=livewire" alt="Livewire 3"></a>
  <a href="https://firebase.google.com"><img src="https://img.shields.io/badge/Backend-Firebase-ffca28.svg?style=for-the-badge&logo=firebase" alt="Powered by Firebase"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/UI-TailwindCSS-06B6D4.svg?style=for-the-badge&logo=tailwindcss" alt="Styled with TailwindCSS"></a>
  <a href="https://github.com/Clis3n/observa-web/blob/main/LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge" alt="License: MIT"></a>
</p>

---

## 📍 Tentang Proyek

**Observa Web** adalah platform dashboard berbasis web yang berfungsi sebagai pendamping untuk aplikasi mobile **[Observa di Google Play](https://play.google.com/store/apps/details?id=com.observa.app)**. Proyek ini dibangun untuk memungkinkan pengguna mengelola, memvisualisasikan, dan mengedit data geospasial mereka (titik lokasi dan rute) yang telah mereka kumpulkan melalui aplikasi Android, semuanya dari kenyamanan browser desktop.

Platform ini disinkronkan secara real-time dengan database Firebase, memastikan konsistensi data antara perangkat mobile dan web.

### ✨ Fitur Utama

-   🗺️ **Visualisasi Data Interaktif:** Semua data titik lokasi dan rute ditampilkan secara visual di atas peta interaktif menggunakan Mapbox GL JS.
-   🗂️ **Manajemen Data Terpusat:** Lihat, filter, dan kelola semua catatan perjalanan Anda dalam satu sidebar yang informatif.
-   🔄 **Sinkronisasi Real-time:** Perubahan yang dibuat di aplikasi mobile akan langsung terlihat di dashboard web, dan sebaliknya, berkat sinkronisasi dengan Firebase Firestore.
-   ✏️ **Detail & Edit Data:**
    -   Klik item di sidebar untuk secara otomatis melakukan zoom ke lokasinya di peta.
    -   Edit judul dan deskripsi data Anda melalui modal yang intuitif.
    -   **[BARU]** Edit data spasial langsung dari peta: geser marker (titik) atau ubah bentuk garis (rute) dengan mudah.
-   🔐 **Otentikasi Aman:** Login aman menggunakan akun Google atau dengan memindai kode QR dari aplikasi mobile Observa.

---

## 📱 Dapatkan Pengalaman Penuh dengan Aplikasi Mobile!

Dashboard web ini dirancang untuk melengkapi aplikasi mobile Observa. Untuk mendapatkan pengalaman terbaik dan mengumpulkan data di lapangan, unduh aplikasi kami sekarang!

<a href="https://play.google.com/store/apps/details?id=com.observa.app" target="_blank">
  <img src="https://play.google.com/intl/en_us/badges/static/images/badges/id_badge_web_generic.png" width="200" alt="Get it on Google Play">
</a>

Dengan aplikasi mobile, Anda dapat:
-   📍 Mencatat titik lokasi dengan koordinat GPS presisi.
-   🗺️ Merekam rute perjalanan Anda di latar belakang.
-   📤 Mengimpor dan mengekspor data dalam format KML/KMZ dan Excel.
-   🎨 Membuat layout peta kustom yang siap untuk dicetak atau dibagikan.

---

## 📸 Tampilan Aplikasi Web

<!-- PENTING: Ganti URL di bawah ini dengan URL screenshot dashboard Anda yang sebenarnya -->
<p align="center">
  <img src="https://raw.githubusercontent.com/Clis3n/observa-web/main/public/landing_assets/image/image-1.png" alt="Tampilan Dashboard" width="48%">
   
  <img src="https://raw.githubusercontent.com/Clis3n/observa-web/main/public/landing_assets/image/image-2.png" alt="Tampilan Login" width="48%">
</p>

---

## 🚀 Teknologi yang Digunakan

-   **Backend:** [Laravel 11](https://laravel.com)
-   **Frontend:**
    -   [Livewire 3](https://livewire.laravel.com) untuk komponen interaktif.
    -   [Alpine.js](https://alpinejs.dev) untuk interaktivitas frontend.
    -   [Tailwind CSS](https://tailwindcss.com) untuk styling.
    -   [Vite](https://vitejs.dev) untuk kompilasi aset.
-   **Database:** [Google Firebase (Firestore)](https://firebase.google.com) sebagai database NoSQL real-time.
-   **Pemetaan:** [Mapbox GL JS](https://www.mapbox.com/mapbox-gljs) & [Mapbox Draw](https://github.com/mapbox/mapbox-gl-draw) untuk visualisasi dan editing peta.
-   **Otentikasi:** [Firebase Authentication](https://firebase.google.com/docs/auth)

---

## 🛠️ Panduan Instalasi & Setup Lokal

Ikuti langkah-langkah ini untuk menjalankan proyek Observa Web di lingkungan lokal Anda.

### Prasyarat

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   Akun Firebase dengan Firestore dan Authentication diaktifkan.

### Langkah-langkah Instalasi

1.  **Clone Repositori**
    ```bash
    git clone https://github.com/Clis3n/observa-web.git
    cd observa-web
    ```

2.  **Install Dependensi PHP**
    ```bash
    composer install
    ```

3.  **Install Dependensi JavaScript**
    ```bash
    npm install
    ```

4.  **Setup File `.env`**
    -   Salin file `.env.example` menjadi `.env`:
        ```bash
        cp .env.example .env
        ```
    -   Buka file `.env` dan isi variabel yang diperlukan, terutama untuk koneksi Firebase. Anda perlu mendapatkan kredensial Firebase Anda (file JSON) dan menyimpannya sesuai dengan variabel `FIREBASE_CREDENTIALS`.
        ```dotenv
        APP_NAME=Observa
        APP_URL=http://127.0.0.1:8000

        # Kredensial Firebase
        FIREBASE_PROJECT_ID="your-project-id"
        FIREBASE_API_KEY="your-api-key"
        FIREBASE_AUTH_DOMAIN="your-auth-domain"
        # ... dan variabel Firebase lainnya
        ```

5.  **Generate Kunci Aplikasi**
    ```bash
    php artisan key:generate
    ```

6.  **Jalankan Server**
    Buka **dua terminal** terpisah di direktori proyek.
    - Di terminal pertama, jalankan Vite untuk kompilasi aset:
      ```bash
      npm run dev
      ```
    - Di terminal kedua, jalankan server Laravel:
      ```bash
      php artisan serve
      ```

7.  **Selesai!**
    Buka `http://127.0.0.1:8000` di browser Anda untuk melihat landing page.

---

## 📂 Struktur Proyek Penting

-   `app/Http/Controllers/Auth/FirebaseLoginController.php`: Mengelola logika login dan otentikasi.
-   `app/Livewire/Dashboard.php`: Komponen utama yang menangani semua logika backend untuk halaman dashboard.
-   `app/Services/FirebaseService.php`: Service class yang bertanggung jawab untuk semua interaksi dengan Firebase Firestore.
-   `resources/views/landing.blade.php`: Tampilan untuk landing page.
-   `resources/views/auth/login.blade.php`: Tampilan untuk halaman login.
-   `resources/views/livewire/dashboard.blade.php`: Tampilan untuk dashboard, berisi layout sidebar, peta, dan semua skrip Mapbox.
-   `resources/js/app.js`: Titik masuk untuk aset JavaScript, tempat library Mapbox diimpor.
-   `routes/web.php`: Mendefinisikan semua rute web untuk aplikasi.

---

## 🤝 Kontribusi

Kontribusi, isu, dan permintaan fitur sangat diterima! Jangan ragu untuk membuka *issue* baru atau membuat *pull request*.

1.  Fork proyek ini.
2.  Buat branch fitur baru (`git checkout -b feature/AmazingFeature`).
3.  Commit perubahan Anda (`git commit -m 'Add some AmazingFeature'`).
4.  Push ke branch (`git push origin feature/AmazingFeature`).
5.  Buka sebuah Pull Request.

---

## 📜 Lisensi

Didistribusikan di bawah Lisensi MIT. Lihat `LICENSE` untuk informasi lebih lanjut.

---

<p align="center">
  <a href="https://clis3n.github.io/TermsofUse-OBSERVA/" target="_blank">Syarat Penggunaan</a> | 
  <a href="https://clis3n.github.io/PrivacyPolicy-OBSERVA/" target="_blank">Kebijakan Privasi</a>
</p>

<p align="center">
  Dibuat dengan 💛 oleh <b>OBSERVA</b>
</p>
