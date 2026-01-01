<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();

        // Check if user is vendor using both methods
        if ($user->hasRole('vendor') || $user->role === 'vendor') {
            return $next($request);
        }

        // Redirect back with error message
        return redirect()->route('menu.browse')->with('error', 'Access denied. Vendor privileges required.');
    }
}
