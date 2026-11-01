<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,driver')   ← multiple roles allowed
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check role
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak.');
        }

        // Pending driver → redirect to waiting page (unless already there)
        if ($user->role === 'driver' && $user->isPending()) {
            if (! $request->routeIs('driver.pending')) {
                return redirect()->route('driver.pending');
            }
        }

        // Rejected account → force logout
        if ($user->status === 'rejected') {
            auth()->logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah ditolak oleh admin.']);
        }

        return $next($request);
    }
}
