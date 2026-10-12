<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $allowedRoles = ['admin', 'kasir', 'dapur', 'owner'];

        if (! in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator atau Pegawai.');
        }

        return $next($request);
    }
}
