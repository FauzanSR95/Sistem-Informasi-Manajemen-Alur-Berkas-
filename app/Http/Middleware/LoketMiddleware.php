<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoketMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role->value === 'loket') {
        return $next($request);
    }
        abort(403, 'AKSES DITOLAK'); // Tampilkan halaman error jika bukan role-nya
    }
}