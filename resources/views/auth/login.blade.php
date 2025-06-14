<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OBSERVA') }} - Login</title>

    <!-- Ikon (Favicon) -->
    <link rel="icon" href="{{ asset('landing_assets/image/icon.svg') }}" type="image/svg+xml">

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vite (Tailwind CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>

        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }
    </style>

    <!-- PERBAIKAN: Pindahkan script handler ke head dan gunakan listener Alpine.js -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('loginHandler', () => ({
                showQr: false,
                unsubscribeQrListener: null,
                showLoading() {
                    const spinner = document.getElementById('loading-spinner');
                    if(spinner) spinner.style.display = 'flex';
                },
                hideLoading() {
                    const spinner = document.getElementById('loading-spinner');
                    if(spinner) spinner.style.display = 'none';
                },
                async postTokenToServer(idToken) {
                    this.showLoading();
                    try {
                        const response = await fetch("{{ route('login.firebase') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ idToken })
                        });
                        const data = await response.json();
                        if (response.ok && data.status === 'success') {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message || 'Login gagal. Silakan coba lagi.');
                            this.hideLoading();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat proses login.');
                        this.hideLoading();
                    }
                },
                signInWithGoogle() {
                    const provider = new window.firebase.GoogleAuthProvider();
                    window.firebase.signInWithPopup(window.firebase.auth, provider)
                        .then(result => result.user.getIdToken())
                        .then(idToken => this.postTokenToServer(idToken))
                        .catch(error => {
                            console.error("Google Sign-In Error:", error);
                            if (error.code !== 'auth/popup-closed-by-user') {
                                alert("Gagal login dengan Google: " + error.message);
                            }
                        });
                },
                generateAndShowQr() {
                    this.showQr = true;
                    const qrToken = 'login_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    const qrcodeContainer = document.getElementById('qrcode');
                    qrcodeContainer.innerHTML = '';
                    new QRCode(qrcodeContainer, { text: qrToken, width: 220, height: 220, colorDark : "#161616", colorLight : "#ffffff" });
                    this.listenForQrLogin(qrToken);
                },
                listenForQrLogin(token) {
                    if (this.unsubscribeQrListener) this.unsubscribeQrListener();

                    const docRef = window.firebase.doc(window.firebase.db, "web_logins", token);
                    this.unsubscribeQrListener = window.firebase.onSnapshot(docRef, (doc) => {
                        if (doc.exists() && doc.data().idToken) {
                            this.unsubscribeQrListener();
                            this.postTokenToServer(doc.data().idToken);
                        }
                    });
                }
            }));
        });
    </script>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 px-4">
        <!-- Logo -->
        <div class="mb-8">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <img src="{{ asset('landing_assets/image/icon.svg') }}" alt="Observa Icon" class="h-12 w-auto">
                <span class="text-4xl font-bold tracking-widest">
                    <span class="text-gray-800">OBSE</span><span class="text-yellow-500">RVA</span>
                </span>
            </a>
        </div>

        <!-- Card Login -->
        <div x-data="loginHandler" class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg relative">
            <div id="loading-spinner" style="display: none;" class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 rounded-lg">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-yellow-500"></div>
            </div>

            <!-- Tampilan Awal (Pilihan Login) -->
            <div x-show="!showQr" x-transition>
                <h2 class="text-center text-2xl font-bold mb-2 text-gray-800">Masuk ke Akunmu</h2>
                <p class="text-center text-gray-500 mb-8">Pilih salah satu metode untuk melanjutkan.</p>

                <button @click="signInWithGoogle()" class="w-full flex items-center justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-md font-medium text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-300">
                    <i class="fab fa-google text-xl mr-3"></i> Lanjutkan dengan Google
                </button>
                <div class="my-6 flex items-center"><div class="flex-grow border-t border-gray-200"></div><span class="flex-shrink mx-4 text-gray-400 text-sm">atau</span><div class="flex-grow border-t border-gray-200"></div></div>
                <button @click="generateAndShowQr()" class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-md shadow-sm bg-gray-800 text-md font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
                    <i class="fas fa-qrcode text-xl mr-3"></i> Scan QR dari Aplikasi
                </button>
            </div>

            <!-- Tampilan QR Code -->
            <div x-show="showQr" style="display: none;" x-transition>
                <h2 class="text-center text-2xl font-bold mb-2 text-gray-800">Scan QR Code</h2>
                <p class="text-center text-gray-500 mb-6 text-sm">Buka aplikasi Observa, masuk ke Pengaturan dan pilih "Login ke Web" untuk memindai kode ini.</p>
                <div id="qrcode" class="flex justify-center items-center my-4 p-4 bg-white rounded-lg border"></div>
                <div class="flex justify-center items-center"><div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-gray-500 mr-2"></div><p class="text-center text-sm text-gray-500">Menunggu otentikasi...</p></div>
                <button @click="showQr = false; if (unsubscribeQrListener) unsubscribeQrListener();" class="mt-6 w-full text-center text-yellow-600 hover:text-yellow-500 text-sm font-semibold">← Kembali</button>
            </div>
        </div>
    </div>

    <!-- Firebase SDK (dari CDN) -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
        import { getFirestore, doc, onSnapshot } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);
        window.firebase = { app, auth, db, GoogleAuthProvider, signInWithPopup, doc, onSnapshot };
    </script>
</body>
</html>
