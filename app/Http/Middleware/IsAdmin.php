<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN memiliki role 'admin'
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request); // Lolos pengecekan, silakan lanjut ke Controller
        }

        // Jika bukan admin, tolak dengan status 403 Forbidden
        return response()->json([
            'message' => 'Akses ditolak. Fitur ini khusus Admin.'
        ], 403);
    }
}