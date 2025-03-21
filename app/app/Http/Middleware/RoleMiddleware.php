<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($role) {
            $user = Auth::user();
            // Check both the role column and Spatie roles
            if ($user->role !== $role && !$user->hasRole($role)) {
                if ($user->role === 'client' || $user->hasRole('client')) {
                    return redirect()->route('shop');
                }
                if ($user->role === 'admin' || $user->hasRole('admin')) {
                    return redirect()->route('dashboard');
                }
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
