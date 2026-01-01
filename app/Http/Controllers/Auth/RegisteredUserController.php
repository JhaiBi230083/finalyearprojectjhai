<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:student,staff,vendor'],
            'faculty' => ['required_if:role,student,staff', 'nullable', 'string', 'max:255'],
            'matric_number' => ['required_if:role,student', 'nullable', 'string', 'max:50', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            'terms.required' => 'You must accept the terms and conditions.',
            'terms.accepted' => 'You must accept the terms and conditions.',
            'faculty.required_if' => 'The faculty field is required for students and staff.',
            'matric_number.required_if' => 'The matric number field is required for students.',
            'matric_number.unique' => 'This matric number is already registered.',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ];

        // Add conditional fields
        if ($request->role === 'student' || $request->role === 'staff') {
            $userData['faculty'] = $request->faculty;
        }

        if ($request->role === 'student') {
            $userData['matric_number'] = $request->matric_number;
        }

        $user = User::create($userData);

        // Assign role using Spatie Permission
        $user->assignRole($request->role);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))->with('success', 'Account created successfully! Welcome to Smart Campus Canteen!');
    }
}
