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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, is_array($roles) ? $roles : explode('|', $roles))) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses sumber daya ini.',
            ], 403);
        }

        $rolesArray = is_array($roles) ? $roles : explode('|', $roles);

        if (!in_array($user->role, $rolesArray)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses sumber daya ini.',
            ], 403);
        }

        return $next($request);
    }
}
