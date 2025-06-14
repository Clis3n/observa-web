<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Session;

class FirebaseLoginController extends Controller
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    /**
     * Menampilkan halaman login.
     */
    public function showLoginForm()
    {
        // Jika sudah ada sesi, langsung ke dashboard
        if (Session::has('firebase_user_id')) {
            return redirect()->route('dashboard');
        }

        // Ini bagian yang paling penting untuk memperbaiki 404.
        // Pastikan baris ini ada dan nama view-nya benar.
        return view('auth.login');
    }

    // ... (sisa metode handleLogin dan handleLogout) ...
    public function handleLogin(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $this->firebaseAuth->getUser($uid);

            Session::put('firebase_user_id', $user->uid);
            Session::put('firebase_user_name', $user->displayName);
            Session::put('firebase_user_email', $user->email);
            Session::put('firebase_user_photo_url', $user->photoUrl);

            return response()->json(['status' => 'success', 'redirect_url' => route('dashboard')]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Login failed: ' . $e->getMessage()], 401);
        }
    }

    public function handleLogout(Request $request)
    {
        Session::flush();
        return redirect()->route('login');
    }
}
