<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirebaseAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('firebase_user_id')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
