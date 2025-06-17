<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check() || auth()->user()->rol !== $role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }
            
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
