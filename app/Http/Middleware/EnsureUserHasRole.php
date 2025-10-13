<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:owner') or ->middleware('role:owner,admin')
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message'=>'Unauthenticated'], 401);
        }

        // allow if user's role is in the given roles
        if (!in_array($user->role, $roles)) {
            return response()->json(['message'=>'Unauthorized (role)'], 403);
        }

        return $next($request);
    }
}
