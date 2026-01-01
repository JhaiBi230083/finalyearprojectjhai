<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();

        // Custom redirect based on user role
        $redirectTo = match($user->role) {
            'admin' => '/admin',
            'vendor' => '/dashboard',
            'staff' => '/dashboard',
            'student' => '/dashboard',
            default => route('dashboard', absolute: false)
        };

        return redirect()->intended($redirectTo)->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to dashboard instead of home
        return redirect()->route('dashboard');
    }

    protected function redirectBasedOnRole($user): RedirectResponse
    {
        // Check role using both Spatie and direct role attribute for compatibility
        if ($user->hasRole('admin') || $user->role === 'admin') {
            return redirect()->intended('/admin')->with('success', 'Welcome back, Admin!');
        }

        if ($user->hasRole('vendor') || $user->role === 'vendor') {
            return redirect()->intended(route('vendor.menu'))->with('success', 'Welcome back! Manage your menu items here.');
        }

        if ($user->hasRole('staff') || $user->role === 'staff') {
            return redirect()->intended(route('staff.menu'))->with('success', 'Welcome back! Manage canteen operations here.');
        }

        // Default redirect for students and other roles
        return redirect()->intended(route('menu.browse'))->with('success', 'Welcome back! Browse our delicious menu.');
    }
}
