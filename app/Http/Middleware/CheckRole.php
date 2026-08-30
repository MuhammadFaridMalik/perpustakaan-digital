<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        // Super Admin punya akses tertinggi, otomatis lolos ke semua route ber-role
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke resource ini.',
            ], 403);
        }

        return $next($request);
    }
}
