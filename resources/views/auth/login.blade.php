<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Smart Campus Canteen</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --primary: #1a1a1a;
            --primary-light: #404040;
            --secondary: #8b8b8b;
            --accent: #2563eb;
            --background: #f9fafb;
            --surface: #ffffff;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', 'Figtree', sans-serif;
            background-color: var(--background);
            min-height: 100vh;
            color: #1a1a1a;
        }

        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05), 0 5px 10px rgba(0, 0, 0, 0.025);
        }

        .input-focus {
            transition: all 0.2s ease;
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            border-color: var(--accent);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #9ca3af;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider:not(:empty)::before {
            margin-right: 1em;
        }

        .divider:not(:empty)::after {
            margin-left: 1em;
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen flex flex-col items-center justify-center p-4 animate-fade-in">
    <!-- Login Container -->
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-10">
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center shadow-md">
                    <span class="text-2xl text-white">🍽️</span>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Smart Campus Canteen</h1>
            <p class="text-gray-600">Sign in to access your account</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-xl card-shadow p-8">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-3"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                            placeholder="you@example.com"
                        >
                    </div>
                    @error('email')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                            placeholder="Enter your password"
                        >
                    </div>
                    @error('password')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full btn-primary py-3 px-4 rounded-lg font-semibold text-base">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-8 mb-6">
                <div class="divider text-sm">Or continue with</div>
            </div>



            <!-- Register Link -->
            <div class="mt-8 text-center">
                <p class="text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        Create account
                    </a>
                </p>
            </div>
        </div>



        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} Smart Campus Canteen. All rights reserved.
            </p>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mt-4 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</div>

<script>
    // Form interaction enhancements
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('input');

        inputs.forEach(input => {
            // Add focus styling on parent
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-blue-100');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-blue-100');
            });
        });

        // Button click effect
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                this.classList.add('active');
                setTimeout(() => {
                    this.classList.remove('active');
                }, 300);
            });
        }

        // Social login button hover effects
        const socialButtons = document.querySelectorAll('button[type="button"]');
        socialButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
</body>
</html>
