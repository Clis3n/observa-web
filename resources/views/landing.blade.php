<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observa - Catat, Petakan, dan Bagikan Duniamu</title>
    <meta name="description" content="Observa adalah aplikasi pencatatan berbasis lokasi yang presisi, dirancang untuk surveyor, peneliti, dan petualang. Buat catatan, rekam rute, dan desain peta kustom dengan mudah.">

    <!-- Ikon untuk tab browser (Favicon) dari path lokal -->
    <link rel="icon" href="{{ asset('landing_assets/image/icon.svg') }}" type="image/svg+xml">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* === CSS Variabel & Reset === */
        :root {
            --primary-color: #FBBC05; /* Golden Orange */
            --dark-color: #161616;
            --light-color: #FFFFFF;
            --text-color: #333333;
            --gray-color: #f4f4f4;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            /* PERBAIKAN: scroll-padding-top dihapus dari sini agar JS bisa mengontrolnya secara penuh */
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-color);
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 0 2rem;
        }

        h1, h2, h3 {
            font-weight: 700;
            color: var(--dark-color);
        }

        h1 {
            font-size: 2.8rem;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        h2 {
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        p {
            font-size: 1rem;
            margin: 0.75rem 0;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
        }

        ul {
            list-style: none;
        }

        /* === Tombol (Buttons) === */
        .btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-secondary {
            background-color: var(--light-color);
            color: var(--dark-color);
            border-color: var(--dark-color);
        }

        .btn-secondary:hover {
            background-color: var(--dark-color);
            color: var(--light-color);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        /* === Header & Navbar === */
        .header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            height: var(--header-height);
            width: 100%;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            height: 35px;
            width: auto;
        }

        .logo-text {
            font-size: 1.7rem;
            font-weight: bold;
            letter-spacing: 0.2em;
        }

        .logo-black {
            color: var(--dark-color);
        }

        .logo-gold {
            color: var(--primary-color);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: var(--text-color);
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .hamburger {
            display: none;
            cursor: pointer;
        }

        .bar {
            display: block;
            width: 25px;
            height: 3px;
            margin: 5px auto;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
            background-color: var(--dark-color);
        }

        /* === Section Sizing & Hero Layout === */
        .full-screen {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 5rem 2rem;
        }

        .hero {
            height: calc(100vh - var(--header-height));
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 0;
        }

        .hero .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            flex-shrink: 0;
        }

        .hero-content .subheading {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .hero-buttons {
            margin-top: 2.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-image {
            margin-top: 1.5rem;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 0;
        }

        .hero-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .section-light {
            background-color: var(--light-color);
        }

        .section-dark {
            background-color: var(--gray-color);
        }

        /* === Features Section === */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: var(--light-color);
            padding: 2rem;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .feature-card .icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        /* === Showcase Section === */
        .showcase-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .showcase-text {
            padding-right: 2rem;
        }

        .showcase-image img {
            border-radius: 10px;
            max-height: 450px;
            width: 100%;
            object-fit: contain;
        }

        .showcase-text h3 {
             font-size: 1.8rem;
             margin-bottom: 1rem;
        }

        .showcase-grid.reverse .showcase-text {
            order: 2;
            padding-right: 0;
            padding-left: 2rem;
        }

        .showcase-grid.reverse .showcase-image {
            order: 1;
        }

        /* === Final CTA Section === */
        .cta-section {
            background: var(--dark-color);
            color: var(--light-color);
        }

        .cta-section h2 {
            color: var(--light-color);
        }

        .cta-section p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 2rem auto;
            color: #ccc;
        }

        /* === Footer === */
        .footer {
            background: #111;
            color: #ccc;
            padding: 3rem 0;
            text-align: center;
        }

        .footer-links {
            margin-bottom: 1rem;
        }

        .footer-links a {
            color: #ccc;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* === Navigasi Panah === */
        #navigation-arrows {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-arrow {
            width: 50px;
            height: 50px;
            background-color: var(--dark-color);
            color: var(--light-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .nav-arrow:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
            color: var(--light-color);
        }

        .nav-arrow.hidden {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.8);
        }

        /* === Animasi === */
        .reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* === Media Queries untuk Responsif === */
        @media (max-width: 992px) {
            .hamburger { display: block; }
            .hamburger.active .bar:nth-child(2) { opacity: 0; }
            .hamburger.active .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
            .hamburger.active .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
            .nav-menu { position: fixed; left: -100%; top: var(--header-height); flex-direction: column; background-color: var(--light-color); width: 100%; height: calc(100vh - var(--header-height)); text-align: center; transition: 0.3s; gap: 2rem; padding-top: 2rem; }
            .nav-menu.active { left: 0; }
            .nav-item { margin: 16px 0; }
        }

        @media (max-width: 768px) {
            h1 { font-size: 2.2rem; }
            h2 { font-size: 1.8rem; }
            .full-screen, .hero { min-height: auto; height: auto; padding: 5rem 2rem; }
            .hero { padding-top: 3rem; padding-bottom: 3rem; }
            .showcase-grid { grid-template-columns: 1fr; text-align: center; }
            .showcase-text { padding: 0; margin-bottom: 2rem; }
            .showcase-grid.reverse .showcase-text { order: 1; padding: 0; }
            .showcase-grid.reverse .showcase-image { order: 2; }
            #navigation-arrows { bottom: 1rem; right: 1rem; }
            .nav-arrow { width: 45px; height: 45px; font-size: 1rem; }
        }
    </style>
</head>
<body>

    <header class="header">
        <nav class="navbar container">
            <a href="#hero" class="logo">
                <img src="{{ asset('landing_assets/image/icon.svg') }}" alt="Observa Icon" class="logo-icon">
                <span class="logo-text">
                    <span class="logo-black">OBSE</span><span class="logo-gold">RVA</span>
                </span>
            </a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#features" class="nav-link">Fitur</a></li>
                <li class="nav-item"><a href="#showcase-1" class="nav-link">Showcase</a></li>
                <li class="nav-item">
                    <a href="https://play.google.com/store/apps/details?id=com.observa.app" class="btn btn-primary" target="_blank">Unduh</a>
                </li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero scroll-section" id="hero">
            <div class="container">
                <div class="hero-content reveal">
                    <h1>Mulai perjalananmu dan catat setiap langkahnya</h1>
                    <p class="subheading">
                        Aplikasi pencatatan berbasis lokasi yang presisi, dirancang untuk surveyor, peneliti, petualang, dan siapa saja yang membutuhkan data geografis akurat.
                    </p>
                    <div class="hero-buttons">
                        <a href="https://play.google.com/store/apps/details?id=com.observa.app" class="btn btn-primary" target="_blank">
                            <i class="fab fa-google-play"></i> Dapatkan di Google Play
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-desktop"></i> Buka Versi Web
                        </a>
                    </div>
                </div>
                <div class="hero-image reveal">
                    <img src="{{ asset('landing_assets/image/hero-illustration.svg') }}" alt="Tampilan aplikasi Observa di smartphone">
                </div>
            </div>
        </section>

        <section id="features" class="section section-dark full-screen scroll-section">
            <div class="container">
                <h2 class="reveal">Fitur Unggulan</h2>
                <div class="features-grid">
                     <div class="feature-card reveal"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><h3>Pencatatan Presisi</h3><p>Simpan catatan dengan koordinat, tanggal, waktu, dan deskripsi. Ambil lokasimu saat ini dengan sekali sentuh.</p></div>
                     <div class="feature-card reveal"><div class="icon"><i class="fas fa-route"></i></div><h3>Perekaman Rute</h3><p>Rekam perjalananmu secara otomatis dengan layanan latar belakang, dan visualisasikan rutemu langsung di peta.</p></div>
                     <div class="feature-card reveal"><div class="icon"><i class="fas fa-drafting-compass"></i></div><h3>Editor Peta Kustom</h3><p>Buat layout peta profesional. Tambahkan judul, legenda, skala, dan keterangan untuk ekspor menjadi gambar peta yang informatif.</p></div>
                     <div class="feature-card reveal"><div class="icon"><i class="fas fa-file-export"></i></div><h3>Impor & Ekspor Fleksibel</h3><p>Ekspor data ke Excel (.xlsx) untuk analisis, atau KML/KMZ untuk integrasi dengan software GIS seperti Google Earth & ArcGIS.</p></div>
                     <div class="feature-card reveal"><div class="icon"><i class="fas fa-cloud-upload-alt"></i></div><h3>Sinkronisasi Cloud</h3><p>Datamu aman dan tersinkronisasi dengan akun Google-mu. Akses catatan dan rutemu kapan saja.</p></div>
                     <div class="feature-card reveal"><div class="icon"><i class="fas fa-layer-group"></i></div><h3>Manajemen Data Mudah</h3><p>Kelola semua data titik dan rute dalam satu daftar. Lakukan pemilihan, hapus, dan ekspor data secara massal dengan mudah.</p></div>
                </div>
            </div>
        </section>

        <section id="showcase-1" class="section section-light full-screen scroll-section">
            <div class="container">
                 <div class="showcase-grid">
                    <div class="showcase-text reveal">
                        <h3>Dirancang untuk Efisiensi di Lapangan</h3>
                        <p>Observa menghilangkan kerumitan pencatatan data geografis. Antarmuka yang bersih dan intuitif memungkinkanmu fokus pada observasi, bukan pada aplikasi.</p>
                        <p>Baik kamu sedang melakukan survei lahan, penelitian ekologi, atau sekadar menjelajahi alam, Observa adalah partner digital yang bisa diandalkan.</p>
                        <a href="#features" class="btn btn-primary" style="margin-top: 1rem;">Lihat Semua Fitur</a>
                    </div>
                    <div class="showcase-image reveal">
                        <img src="{{ asset('landing_assets/image/showcase-1.svg') }}" alt="Contoh penggunaan aplikasi Observa">
                    </div>
                </div>
            </div>
        </section>

        <section id="showcase-2" class="section section-dark full-screen scroll-section">
            <div class="container">
                 <div class="showcase-grid reverse">
                    <div class="showcase-text reveal">
                        <h3>Dari Data Mentah Menjadi Peta Profesional</h3>
                        <p>Fitur Editor Peta adalah keunggulan utama Observa. Ubah kumpulan titik dan garis-garismu menjadi sebuah peta siap cetak yang lengkap.</p>
                        <ul style="list-style-position: inside; padding-left: 1rem;">
                            <li>Kustomisasi legenda dan warna.</li>
                            <li>Atur skala peta dengan presisi.</li>
                            <li>Lengkapi dengan inset peta dan arah utara.</li>
                            <li>Unduh sebagai gambar PNG berkualitas tinggi.</li>
                        </ul>
                    </div>
                    <div class="showcase-image reveal">
                        <img src="{{ asset('landing_assets/image/showcase-2.svg') }}" alt="Fitur editor peta di aplikasi Observa">
                    </div>
                </div>
            </div>
        </section>

        <section id="cta" class="cta-section full-screen scroll-section">
            <div class="container reveal">
                <h2>Siap Memulai Observasimu?</h2>
                <p>Unduh Observa sekarang dan ubah caramu berinteraksi dengan dunia di sekitarmu. Presisi, kekuatan, dan kemudahan ada di ujung jarimu.</p>
                <a href="https://play.google.com/store/apps/details?id=com.observa.app" class="btn btn-primary" target="_blank">
                    <i class="fab fa-google-play"></i> Unduh Gratis di Google Play
                </a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <a href="https://clis3n.github.io/TermsofUse-OBSERVA/" target="_blank">Syarat Penggunaan</a>
                <a href="https://clis3n.github.io/PrivacyPolicy-OBSERVA/" target="_blank">Kebijakan Privasi</a>
            </div>
            <p>© 2024 Observa. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <!-- Tombol Navigasi Panah -->
    <div id="navigation-arrows">
        <a href="#" id="arrow-up" class="nav-arrow hidden"><i class="fas fa-arrow-up"></i></a>
        <a href="#" id="arrow-down" class="nav-arrow"><i class="fas fa-arrow-down"></i></a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revealElements = document.querySelectorAll('.reveal');
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            revealElements.forEach(el => revealObserver.observe(el));

            const hamburger = document.querySelector(".hamburger");
            const navMenu = document.querySelector(".nav-menu");
            hamburger.addEventListener("click", () => {
                hamburger.classList.toggle("active");
                navMenu.classList.toggle("active");
            });
            document.querySelectorAll(".nav-link").forEach(n => n.addEventListener("click", () => {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            }));

            // --- Logika untuk Tombol Navigasi Panah ---
            const sections = document.querySelectorAll('.scroll-section');
            const arrowUp = document.getElementById('arrow-up');
            const arrowDown = document.getElementById('arrow-down');
            let currentSectionIndex = 0;

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        sections.forEach((section, index) => {
                            if (section.id === entry.target.id) {
                                currentSectionIndex = index;
                            }
                        });
                        arrowUp.classList.toggle('hidden', currentSectionIndex === 0);
                        arrowDown.classList.toggle('hidden', currentSectionIndex === sections.length - 1);
                    }
                });
            }, { threshold: 0.5 });

            sections.forEach(section => sectionObserver.observe(section));

            arrowDown.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentSectionIndex < sections.length - 1) {
                    sections[currentSectionIndex + 1].scrollIntoView({ behavior: 'smooth' });
                }
            });

            // [PERBAIKAN] Logika klik panah atas yang dikembalikan ke versi pertama
            arrowUp.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentSectionIndex > 0) {
                    const targetSectionIndex = currentSectionIndex - 1;
                    const targetSection = sections[targetSectionIndex];

                    if (targetSection.id === 'hero') {
                         // Scroll ke paling atas halaman (posisi 0)
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        // Untuk section lain, gunakan metode biasa
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        });
    </script>

</body>
</html>
