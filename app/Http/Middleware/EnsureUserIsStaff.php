<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    /**
     * Handle an incoming request.
     * Ensure the authenticated user is a staff member, blocking reader accounts from admin area.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isReader()) {
            abort(403, 'Akses ditolak. Halaman administrasi hanya dapat diakses oleh staf redaksi TopNews.');
        }

        return $next($request);
    }
}
