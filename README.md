<p align="center">
  <img src="https://raw.githubusercontent.com/Clis3n/observa-web/main/public/landing_assets/image/hero-illustration.svg" alt="Observa Logo" width="120">
</p>

<h1 align="center">Observa Web</h1>

<p align="center">
  Versi web resmi dari aplikasi Observa. Dibangun dengan Laravel dan Firebase untuk memantau, menganalisis, dan mengelola data Anda langsung dari browser.
</p>

<p align="center">
  <!-- Badges/Lencana yang Akurat -->
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/Framework-Laravel-FF2D20.svg?style=for-the-badge&logo=laravel" alt="Built with Laravel">
  </a>
  <a href="https://firebase.google.com">
    <img src="https://img.shields.io/badge/Backend-Firebase-ffca28.svg?style=for-the-badge&logo=firebase" alt="Powered by Firebase">
  </a>
  <a href="https://tailwindcss.com">
    <img src="https://img.shields.io/badge/Styled%20with-TailwindCSS-06B6D4.svg?style=for-the-badge&logo=tailwindcss" alt="Styled with TailwindCSS">
  </a>
  <!-- INI BAGIAN YANG DIPERBAIKI -->
  <a href="https://github.com/Clis3n/observa-web/blob/main/LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge" alt="License: MIT">
  </a>
</p>

---

## 📖 Tentang Proyek

**Observa Web** adalah platform berbasis web yang dirancang sebagai pelengkap dari aplikasi mobile **Observa** di Android. Proyek ini memberikan aksesibilitas yang lebih luas, memungkinkan pengguna untuk mengelola data observasi mereka dari perangkat apa pun dengan browser.

Platform ini dibangun di atas fondasi yang kokoh menggunakan **Framework Laravel**, dengan integrasi ke **Firebase** untuk otentikasi dan manajemen data secara real-time, serta antarmuka yang modern menggunakan **Tailwind CSS**.

<br>

## 📱 Dapatkan Pengalaman Terbaik di Aplikasi Mobile!

Untuk mendapatkan fungsionalitas penuh dan pengalaman terbaik, kami sangat merekomendasikan Anda untuk mengunduh aplikasi **Observa** di Google Play Store!

Dengan aplikasi mobile, Anda akan mendapatkan:
-   **Akses Offline**: Catat data observasi bahkan tanpa koneksi internet.
-   **Notifikasi Real-time**: Dapatkan pemberitahuan penting langsung di ponsel Anda.
-   **Integrasi Penuh dengan Perangkat**: Manfaatkan kamera, GPS, dan sensor lainnya untuk data yang lebih akurat.
-   **Antarmuka yang Dioptimalkan**: Desain yang dibuat khusus untuk kenyamanan penggunaan di perangkat mobile.

<a href="https://play.google.com/store/apps/details?id=com.observa.app" target="_blank">
  <img src="https://play.google.com/intl/en_us/badges/static/images/badges/id_badge_web_generic.png" alt="Get it on Google Play" width="200">
</a>

<br>

## ✨ Fitur Utama Web

-   **📊 Dasbor Analitik**: Visualisasikan data Anda dengan grafik dan bagan yang informatif.
-   **🗂️ Manajemen Data**: Buat, lihat, edit, dan hapus data observasi dengan mudah.
-   **🔒 Otentikasi Aman**: Sistem login yang aman menggunakan Firebase Authentication.
-   **📱 Desain Responsif**: Tampilan yang menyesuaikan dengan sempurna di berbagai ukuran layar.
-   **🔄 Sinkronisasi Data**: Data yang Anda masukkan di web akan tersinkronisasi dengan aplikasi mobile Anda (dan sebaliknya) melalui Firebase.

<br>

## 📸 Tampilan Aplikasi Web

<p align="center">
  <img src="https://raw.githubusercontent.com/Clis3n/observa-web/main/public/landing_assets/image/image-1.png" alt="Tampilan Showcase 1" width="48%">
   
  <img src="https://raw.githubusercontent.com/Clis3n/observa-web/main/public/landing_assets/image/image-2.png" alt="Tampilan Showcase 2" width="48%">
</p>

<br>

## 🛠️ Teknologi yang Digunakan

-   **Backend Framework**: [Laravel](https://laravel.com/)
-   **Backend Service & Database**: [Google Firebase](https://firebase.google.com/) (menggunakan paket `kreait/laravel-firebase`)
-   **Frontend**: [Blade Templates](https://laravel.com/docs/blade) & [Tailwind CSS](https://tailwindcss.com/)
-   **Build Tool**: [Vite](https://vitejs.dev/)

<br>

## 🚀 Memulai (Getting Started)

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

### Prasyarat

Pastikan Anda telah menginstal perangkat lunak berikut:
*   **PHP** (versi 8.1 atau lebih tinggi)
*   **Composer**
*   **Node.js** dan **npm**

### Instalasi & Konfigurasi

1.  **Clone repositori ini:**
    ```bash
    git clone https://github.com/Clis3n/observa-web.git
    cd observa-web
    ```

2.  **Instal dependencies PHP via Composer:**
    ```bash
    composer install
    ```

3.  **Instal dependencies JavaScript via npm:**
    ```bash
    npm install
    ```

4.  **Buat file environment:**
    Salin file `.env.example` menjadi `.env`.
    ```bash
    cp .env.example .env
    ```

5.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```

6.  **Konfigurasi Firebase:**
    *   Buka file `.env` yang baru Anda buat.
    *   Unduh file kredensial *service account* (JSON) dari proyek Firebase Anda.
    *   Atur variabel `FIREBASE_CREDENTIALS` di file `.env` dengan menunjuk ke path file JSON tersebut.
      ```env
      FIREBASE_CREDENTIALS=/path/to/your/firebase_credentials.json
      ```

7.  **Jalankan Server:**
    *   Buka **dua terminal** terpisah di direktori proyek.
    *   Di terminal pertama, jalankan server development Laravel:
      ```bash
      php artisan serve
      ```
    *   Di terminal kedua, jalankan Vite untuk meng-compile aset frontend:
      ```bash
      npm run dev
      ```

8.  Buka browser Anda dan kunjungi `http://127.0.0.1:8000`.

<br>

## 🤝 Berkontribusi

Kontribusi dari Anda sangat kami hargai! Jika Anda ingin membantu mengembangkan Observa Web, silakan:

1.  **Fork** repositori ini.
2.  Buat **Branch** baru untuk fitur Anda (`git checkout -b fitur/NamaFitur`).
3.  **Commit** perubahan Anda (`git commit -m 'Menambahkan fitur A'`).
4.  **Push** ke Branch tersebut (`git push origin fitur/NamaFitur`).
5.  Buka **Pull Request**.

Jangan ragu untuk membuka *issue* jika Anda menemukan bug atau memiliki saran.

<br>

## 📄 Lisensi

Proyek ini didistribusikan di bawah Lisensi MIT. Lihat file `LICENSE` untuk informasi lebih lanjut.

---

<p align="center">
  <a href="https://clis3n.github.io/TermsofUse-OBSERVA/" target="_blank">Syarat Penggunaan</a> | 
  <a href="https://clis3n.github.io/PrivacyPolicy-OBSERVA/" target="_blank">Kebijakan Privasi</a>
</p>

<p align="center">
  Dibuat dengan ❤️ oleh <b>Clis3n</b>
</p>
