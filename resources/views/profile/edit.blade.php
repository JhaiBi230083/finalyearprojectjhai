<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Profile - {{ config('app.name', 'Smart Canteen') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #5a7df8;
            --secondary: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1f2937;
            --light: #f8fafc;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8fafc 100%);
            color: var(--gray-900);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
        }

        /* Glass Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 8px 32px rgba(31, 38, 135, 0.07),
                0 4px 16px rgba(31, 38, 135, 0.05);
        }

        /* Form Styling */
        .form-input {
            transition: all 0.2s ease;
            border: 1px solid var(--gray-300);
            background: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .form-input.error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Button Styling */
        .btn {
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 8px;
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.2);
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.2);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        /* Card Hover Effects */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow:
                0 12px 40px rgba(31, 38, 135, 0.1),
                0 8px 24px rgba(31, 38, 135, 0.08);
        }

        /* Avatar Animation */
        .avatar-container {
            position: relative;
            display: inline-block;
        }

        .avatar-container::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            z-index: 0;
            animation: rotate 4s linear infinite;
            opacity: 0.3;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Navigation */
        .nav-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        /* Statistics Card */
        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        /* Modal Animation */
        .modal-enter {
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Loading Animation */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            color: white;
            margin-top: auto;
        }

        .footer-link {
            color: #cbd5e1;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: white;
        }

        .footer-divider {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
    </style>
</head>
<body>
<!-- Navigation -->
<nav class="nav-gradient shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300 group-hover:bg-white/30 group-hover:scale-105">
                    <i class="fas fa-utensils text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-xl font-bold text-white tracking-tight">Smart Canteen</span>
                    <span class="block text-xs text-white/70 font-medium tracking-wide">Profile Management</span>
                </div>
            </a>

            <!-- Back Button -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-2 px-4 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg transition-all duration-200 group">
                <i class="fas fa-arrow-left text-white text-sm"></i>
                <span class="text-white font-medium text-sm group-hover:translate-x-[-2px] transition-transform duration-200">
                        Back to Dashboard
                    </span>
            </a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="avatar-container mb-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold relative z-10">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Profile Settings</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Manage your account information, security settings, and preferences</p>
            </div>

            <!-- Hidden Success Messages -->
            @if (session('profile-updated'))
                <div id="profileSuccessMessage" class="hidden">{{ session('profile-updated') }}</div>
            @endif
            @if (session('password-updated'))
                <div id="passwordSuccessMessage" class="hidden">{{ session('password-updated') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Profile Information -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Profile Information Card -->
                    <div class="glass-card rounded-2xl p-8 card-hover">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-user-circle text-blue-500"></i>
                                    Profile Information
                                </h2>
                                <p class="text-gray-500 text-sm mt-1">Update your personal details and contact information</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-id-card text-blue-600"></i>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.update') }}" id="profileForm">
                            @csrf
                            @method('patch')

                            <div class="space-y-6">
                                <!-- Name Field -->
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-user text-gray-400 text-sm"></i>
                                        Full Name
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               name="name"
                                               id="name"
                                               value="{{ old('name', $user->name) }}"
                                               required
                                               class="form-input w-full pl-10 pr-4 py-3 rounded-lg @error('name') error @enderror"
                                               placeholder="Enter your full name">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    @error('name')
                                    <div class="flex items-center gap-2 text-sm text-red-600 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-envelope text-gray-400 text-sm"></i>
                                        Email Address
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email"
                                               name="email"
                                               id="email"
                                               value="{{ old('email', $user->email) }}"
                                               required
                                               class="form-input w-full pl-10 pr-4 py-3 rounded-lg @error('email') error @enderror"
                                               placeholder="Enter your email address">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                    @error('email')
                                    <div class="flex items-center gap-2 text-sm text-red-600 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button type="submit"
                                            id="profileSubmitBtn"
                                            class="btn btn-primary px-6 py-3 w-full flex items-center justify-center gap-2">
                                        <i class="fas fa-save"></i>
                                        <span>Save Changes</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Password Update Card -->
                    <div class="glass-card rounded-2xl p-8 card-hover">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-lock text-green-500"></i>
                                    Security Settings
                                </h2>
                                <p class="text-gray-500 text-sm mt-1">Update your password to keep your account secure</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-green-600"></i>
                            </div>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" id="passwordForm">
                            @csrf
                            @method('put')

                            <div class="space-y-6">
                                <!-- Current Password -->
                                <div class="space-y-2">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-key text-gray-400 text-sm"></i>
                                        Current Password
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               name="current_password"
                                               id="current_password"
                                               class="form-input w-full pl-10 pr-4 py-3 rounded-lg @error('current_password') error @enderror"
                                               placeholder="Enter current password">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                    @error('current_password')
                                    <div class="flex items-center gap-2 text-sm text-red-600 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-key text-gray-400 text-sm"></i>
                                        New Password
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               name="password"
                                               id="password"
                                               class="form-input w-full pl-10 pr-4 py-3 rounded-lg @error('password') error @enderror"
                                               placeholder="Enter new password">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                    @error('password')
                                    <div class="flex items-center gap-2 text-sm text-red-600 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        Minimum 8 characters with letters and numbers
                                    </p>
                                </div>

                                <!-- Confirm Password -->
                                <div class="space-y-2">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-check-circle text-gray-400 text-sm"></i>
                                        Confirm Password
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               name="password_confirmation"
                                               id="password_confirmation"
                                               class="form-input w-full pl-10 pr-4 py-3 rounded-lg"
                                               placeholder="Confirm new password">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button type="submit"
                                            id="passwordSubmitBtn"
                                            class="btn btn-primary px-6 py-3 w-full flex items-center justify-center gap-2">
                                        <i class="fas fa-key"></i>
                                        <span>Update Password</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Account Info & Management -->
                <div class="space-y-8">
                    <!-- Account Overview Card -->
                    <div class="glass-card rounded-2xl p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-line text-purple-500"></i>
                                Account Overview
                            </h2>
                            <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs font-semibold rounded-full capitalize">
                                    {{ $user->role }}
                                </span>
                        </div>

                        <!-- User Info -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $user->name }}</h3>
                                    <p class="text-gray-600 text-sm flex items-center gap-1">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                        {{ $user->email }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar-alt"></i>
                                        Joined {{ $user->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="space-y-4">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-gray-400"></i>
                                Account Statistics
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="stat-card p-4 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-2xl font-bold text-gray-900">{{ $user->orders()->count() }}</p>
                                            <p class="text-sm text-gray-600">Total Orders</p>
                                        </div>
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-shopping-bag text-blue-600"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-card p-4 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-2xl font-bold text-gray-900">{{ $user->orders()->where('status', 'completed')->count() }}</p>
                                            <p class="text-sm text-gray-600">Completed</p>
                                        </div>
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone Card -->
                    <div class="glass-card rounded-2xl p-8 border border-red-100 bg-gradient-to-r from-red-50/50 to-red-50/30">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-red-900 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                Danger Zone
                            </h2>
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-trash-alt text-red-600"></i>
                            </div>
                        </div>

                        <p class="text-gray-700 mb-6">
                            Once you delete your account, all your data will be permanently removed. This action cannot be undone.
                        </p>

                        <button onclick="showDeleteModal()"
                                class="btn btn-danger w-full flex items-center justify-center gap-2 py-3">
                            <i class="fas fa-trash-alt"></i>
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Column -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-utensils text-white text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold text-white">Smart Campus Canteen</span>
                            <p class="text-blue-200 text-sm">Revolutionizing campus dining</p>
                        </div>
                    </div>
                    <p class="text-gray-400 max-w-md">
                        A smart solution for campus dining that connects students with delicious meals from campus canteens efficiently and conveniently.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('dashboard') }}" class="footer-link flex items-center gap-2">
                                <i class="fas fa-home text-sm"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('menu.browse') }}" class="footer-link flex items-center gap-2">
                                <i class="fas fa-utensils text-sm"></i>
                                Browse Menu
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('order.history') }}" class="footer-link flex items-center gap-2">
                                <i class="fas fa-history text-sm"></i>
                                Order History
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Contact</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-gray-400">
                            <i class="fas fa-map-marker-alt text-blue-400 mt-1"></i>
                            <span>Universiti Tun Hussein Onn Malaysia</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <i class="fas fa-phone text-blue-400"></i>
                            <span>(+60) 16-787 8547</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <i class="fas fa-envelope text-blue-400"></i>
                            <span>support@smartcanteen.edu</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <div class="footer-divider h-px my-8"></div>

            <!-- Bottom Section -->
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © 2025 Smart Campus Canteen. All rights reserved.
                </div>

                <div class="flex items-center space-x-6">
                    <a href="#" class="footer-link text-sm">
                        Privacy Policy
                    </a>
                    <a href="#" class="footer-link text-sm">
                        Terms of Service
                    </a>
                    <a href="#" class="footer-link text-sm">
                        Cookie Policy
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Delete Account Modal -->
<div id="deleteModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden modal-enter">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Delete Account</h3>
                        <p class="text-sm text-gray-600">This action cannot be undone</p>
                    </div>
                </div>
                <button onclick="hideDeleteModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p class="text-gray-700 mb-6">
                Are you sure you want to delete your account? All of your data will be permanently removed from our servers. This action cannot be undone.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" id="deleteForm">
                @csrf
                @method('delete')

                <div class="space-y-4">
                    <div>
                        <label for="password_delete" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password_delete"
                                   required
                                   class="form-input w-full pl-10 pr-4 py-3 rounded-lg @error('password', 'userDeletion') error @enderror"
                                   placeholder="Enter your password to confirm">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-key"></i>
                            </div>
                        </div>
                        @error('password', 'userDeletion')
                        <div class="flex items-center gap-2 text-sm text-red-600 mt-2">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button"
                                onclick="hideDeleteModal()"
                                class="btn btn-secondary flex-1 py-3">
                            Cancel
                        </button>
                        <button type="submit"
                                id="deleteSubmitBtn"
                                class="btn btn-danger flex-1 py-3 flex items-center justify-center gap-2">
                            <i class="fas fa-trash-alt"></i>
                            Delete Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal Functions
    function showDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hideDeleteModal();
    });

    // Close modal on outside click
    document.getElementById('deleteModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'deleteModal') hideDeleteModal();
    });

    // Form Submission Handlers
    document.addEventListener('DOMContentLoaded', function() {
        // Show success messages
        const profileSuccessMsg = document.getElementById('profileSuccessMessage');
        if (profileSuccessMsg?.textContent.trim()) {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: profileSuccessMsg.textContent.trim(),
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #10b981, #059669)',
                color: '#ffffff',
                iconColor: '#ffffff'
            });
        }

        const passwordSuccessMsg = document.getElementById('passwordSuccessMessage');
        if (passwordSuccessMsg?.textContent.trim()) {
            Swal.fire({
                icon: 'success',
                title: 'Password Updated!',
                text: passwordSuccessMsg.textContent.trim(),
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #10b981, #059669)',
                color: '#ffffff',
                iconColor: '#ffffff'
            });

            // Clear password fields
            document.getElementById('current_password').value = '';
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
        }

        // Profile form submission
        const profileForm = document.getElementById('profileForm');
        const profileSubmitBtn = document.getElementById('profileSubmitBtn');

        if (profileForm && profileSubmitBtn) {
            profileForm.addEventListener('submit', function() {
                profileSubmitBtn.innerHTML = `
                    <i class="fas fa-spinner loading-spinner"></i>
                    <span>Saving...</span>
                `;
                profileSubmitBtn.disabled = true;
                profileSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }

        // Password form submission
        const passwordForm = document.getElementById('passwordForm');
        const passwordSubmitBtn = document.getElementById('passwordSubmitBtn');

        if (passwordForm && passwordSubmitBtn) {
            passwordForm.addEventListener('submit', function() {
                passwordSubmitBtn.innerHTML = `
                    <i class="fas fa-spinner loading-spinner"></i>
                    <span>Updating...</span>
                `;
                passwordSubmitBtn.disabled = true;
                passwordSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }

        // Delete form submission
        const deleteForm = document.getElementById('deleteForm');
        const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');

        if (deleteForm && deleteSubmitBtn) {
            deleteForm.addEventListener('submit', function() {
                deleteSubmitBtn.innerHTML = `
                    <i class="fas fa-spinner loading-spinner"></i>
                    <span>Deleting...</span>
                `;
                deleteSubmitBtn.disabled = true;
                deleteSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }

        // Add input focus effects
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-blue-200');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-blue-200');
            });
        });
    });
</script>
</body>
</html>
