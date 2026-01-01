<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminEmailDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Check if user's email ends with @admin.com
        if (!str_ends_with($user->email, '@admin.com')) {
            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Access denied. You are not an admin.',
                    'redirect' => route('filament.admin.auth.login')
                ], 403);
            }

            // Log out the user
            auth()->logout();

            // Clear the session
            session()->invalidate();
            session()->regenerateToken();

            // Store error message in session for JavaScript to pick up
            session()->flash('admin_access_denied', 'Access denied. You are not an admin.');

            // Redirect to login
            return redirect()->route('filament.admin.auth.login');
        }

        return $next($request);
    }
}
