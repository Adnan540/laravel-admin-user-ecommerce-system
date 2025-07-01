<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->is_admin == 1) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
