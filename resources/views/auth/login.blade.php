<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <a href="/">
                {{-- Ganti dengan logo Anda jika ada --}}
                <h1 class="text-4xl font-bold text-gray-800">Observa Web</h1>
            </a>
        </div>

        <div x-data="loginHandler()" class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div id="loading-spinner" class="hidden absolute inset-0 bg-white bg-opacity-75 items-center justify-center z-50">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
            </div>

            <div x-show="!showQr">
                <h2 class="text-center text-2xl font-bold mb-4">Login ke Akun Anda</h2>
                <p class="text-center text-gray-600 mb-6">Pilih salah satu metode untuk melanjutkan.</p>

                <!-- Tombol Login Google -->
                <button @click="signInWithGoogle()" class="w-full flex items-center justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#4285F4" d="M24 9.5c3.9 0 6.9 1.6 9.1 3.7l6.8-6.8C35.9 2.5 30.5 0 24 0 14.9 0 7.3 5.4 3 12.9l8.3 6.4C12.9 13.2 18.1 9.5 24 9.5z"/><path fill="#34A853" d="M46.2 25.1c0-1.7-.2-3.4-.5-5.1H24v9.6h12.5c-.5 3.1-2.1 5.7-4.6 7.4l7.6 5.9c4.4-4.1 7-10.1 7-17.8z"/><path fill="#FBBC05" d="M11.3 28.3c-.4-1.2-.6-2.5-.6-3.8s.2-2.6.6-3.8l-8.3-6.4C1.2 17.5 0 20.6 0 24s1.2 6.5 3 9.4l8.3-5.1z"/><path fill="#EA4335" d="M24 48c6.5 0 11.9-2.2 15.9-5.9l-7.6-5.9c-2.1 1.4-4.8 2.3-7.9 2.3-5.8 0-10.8-3.9-12.6-9.2L3 36.3C7.3 43.8 14.9 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
                    Lanjutkan dengan Google
                </button>

                <div class="my-4 flex items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink mx-4 text-gray-500">atau</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- Tombol Scan QR -->
                <button @click="generateAndShowQr()" class="w-full flex items-center justify-center py-2 px-4 border border-transparent rounded-md shadow-sm bg-gray-800 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Scan QR dari Aplikasi
                </button>
            </div>

            <div x-show="showQr" style="display: none;">
                <h2 class="text-center text-2xl font-bold mb-4">Scan QR Code</h2>
                <p class="text-center text-gray-600 mb-6">Buka aplikasi Observa di ponsel Anda, masuk ke Pengaturan dan pilih "Login ke Web" untuk memindai kode ini.</p>
                <div id="qrcode" class="flex justify-center items-center my-4"></div>
                <p class="text-center text-sm text-gray-500">Menunggu otentikasi...</p>
                <button @click="showQr = false" class="mt-4 w-full text-center text-indigo-600 hover:text-indigo-500">Kembali</button>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
        import { getFirestore, doc, onSnapshot } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);

        window.firebase = { app, auth, db, GoogleAuthProvider, signInWithPopup, doc, onSnapshot };
    </script>

    <script>
        function loginHandler() {
            return {
                showQr: false,
                unsubscribeQrListener: null,

                showLoading() {
                    document.getElementById('loading-spinner').classList.remove('hidden');
                },

                hideLoading() {
                    document.getElementById('loading-spinner').classList.add('hidden');
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
                            body: JSON.stringify({ idToken: idToken })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message || 'Login failed. Please try again.');
                            this.hideLoading();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred during login.');
                        this.hideLoading();
                    }
                },

                signInWithGoogle() {
                    const provider = new window.firebase.GoogleAuthProvider();
                    window.firebase.signInWithPopup(window.firebase.auth, provider)
                        .then((result) => {
                            result.user.getIdToken().then((idToken) => {
                                this.postTokenToServer(idToken);
                            });
                        }).catch((error) => {
                            console.error("Google Sign-In Error:", error);
                            alert("Gagal login dengan Google: " + error.message);
                        });
                },

                generateAndShowQr() {
                    this.showQr = true;

                    // Generate a unique token for this session
                    const qrToken = 'login_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                    // Clear previous QR code if exists
                    document.getElementById('qrcode').innerHTML = '';

                    // Generate new QR code
                    new QRCode(document.getElementById('qrcode'), {
                        text: qrToken,
                        width: 200,
                        height: 200,
                    });

                    // Listen for changes on the Firestore document
                    this.listenForQrLogin(qrToken);
                },

                listenForQrLogin(token) {
                    // Unsubscribe from previous listener if it exists
                    if (this.unsubscribeQrListener) {
                        this.unsubscribeQrListener();
                    }

                    const docRef = window.firebase.doc(window.firebase.db, "web_logins", token);
                    this.unsubscribeQrListener = window.firebase.onSnapshot(docRef, (doc) => {
                        if (doc.exists() && doc.data().idToken) {
                            // Token found, stop listening and process login
                            this.unsubscribeQrListener();
                            this.postTokenToServer(doc.data().idToken);
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
