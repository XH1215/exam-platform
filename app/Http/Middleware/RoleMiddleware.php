<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * RoleMiddleware – custom middleware to enforce user roles.
 * Usage: ->middleware('role:admin') etc.
 */
class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();
        if (!$user || $user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden - you do not have the required role'
            ], 403);
        }
        return $next($request);
    }
}
