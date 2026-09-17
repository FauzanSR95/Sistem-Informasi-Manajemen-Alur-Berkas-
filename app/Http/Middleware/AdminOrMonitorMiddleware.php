<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrMonitorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userRole = auth()->user()->role->value;
        if ($userRole === 'admin' || $userRole === 'monitor') {
            return $next($request);
        }
        abort(403, 'AKSES DITOLAK');
    }
}