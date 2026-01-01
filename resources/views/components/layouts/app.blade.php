<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Campus Canteen') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert for toast notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary: #64748b;
            --accent: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background-color: var(--gray-50);
            color: var(--gray-800);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Navigation Styles */
        .nav-container {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .nav-link {
            position: relative;
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.875rem;
            letter-spacing: -0.01em;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Logo */
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.125rem;
        }

        .logo-text {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.02em;
        }

        /* Button Styles */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
        }

        .btn-ghost {
            background: transparent;
            color: var(--gray-600);
        }

        .btn-ghost:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        /* Badge Styles */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .badge-student {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-vendor {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-staff {
            background: #fce7f3;
            color: #9f1239;
        }

        .badge-admin {
            background: #ede9fe;
            color: #5b21b6;
        }

        .badge-live {
            background: #dcfce7;
            color: #166534;
            animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-subtle {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            padding: 0 4px;
            border: 2px solid white;
            z-index: 10;
        }

        .notification-badge.pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            width: 24rem;
            max-width: 90vw;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            z-index: 999;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        .notification-list {
            max-height: 20rem;
            overflow-y: auto;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: var(--gray-50);
        }

        .notification-item.unread {
            background-color: #eff6ff;
        }

        .notification-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .notification-message {
            font-size: 0.8125rem;
            color: var(--gray-600);
            line-height: 1.4;
            margin-bottom: 0.5rem;
        }

        .notification-time {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .notification-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: var(--primary);
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .notification-empty {
            padding: 2rem 1rem;
            text-align: center;
            color: var(--gray-500);
        }

        .notification-empty i {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Dropdown */
        .dropdown {
            position: absolute;
            right: 0;
            margin-top: 0.5rem;
            width: 15rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            z-index: 50;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--gray-700);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--primary);
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 0.75rem;
            color: var(--gray-400);
        }

        .dropdown-item:hover i {
            color: var(--primary);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.25rem 0;
        }

        /* Avatar */
        .avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.08);
        }

        /* Alert Styles */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f0fdf4;
            border-left-color: var(--accent);
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border-left-color: var(--danger);
            color: #991b1b;
        }

        .alert-info {
            background: #eff6ff;
            border-left-color: var(--primary);
            color: #1e40af;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
        }

        .modal-overlay.show {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            padding: 2rem;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-icon {
            width: 64px;
            height: 64px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--danger);
            font-size: 1.75rem;
        }

        /* Mobile Menu */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .mobile-menu.open {
            max-height: 600px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        /* Footer */
        .footer {
            background: white;
            border-top: 1px solid var(--gray-200);
            margin-top: 4rem;
        }

        .footer-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
            letter-spacing: -0.01em;
        }

        .footer-link {
            color: var(--gray-600);
            font-size: 0.875rem;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .page-title {
                font-size: 1.5rem;
            }

            .notification-dropdown {
                position: fixed;
                top: 4rem;
                right: 1rem;
                left: 1rem;
                width: auto;
            }
        }

        /* Utility Classes */
        .text-muted {
            color: var(--gray-600);
        }

        .divider {
            height: 1px;
            background: var(--gray-200);
        }

        /* Livewire notification bell specific */
        .notification-bell-wrapper {
            position: relative;
        }
    </style>

    @livewireStyles
</head>
<body class="antialiased">
<div class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="nav-container sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="logo-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <span class="logo-text">Smart Canteen</span>
                    </a>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:flex items-center ml-10 space-x-1">
                        @php
                            $currentRoute = request()->route()->getName();
                            $user = auth()->user();
                            $role = $user ? $user->role : null;
                        @endphp

                        <a href="{{ route('dashboard') }}"
                           class="nav-link px-4 py-2 {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                            Dashboard
                        </a>

                        @if(!$user || in_array($role, ['student', 'staff', null]))
                            <a href="{{ route('menu.browse') }}"
                               class="nav-link px-4 py-2 {{ $currentRoute === 'menu.browse' ? 'active' : '' }}">
                                Menu
                            </a>
                        @endif

                        @if($role === 'vendor')
                            <a href="{{ route('vendor.orders') }}"
                               class="nav-link px-4 py-2 {{ $currentRoute === 'vendor.orders' ? 'active' : '' }}">
                                <span>Orders</span>
                                <span class="badge badge-live ml-2">Live</span>
                            </a>
                        @endif

                        @auth


                            @if($role === 'student')
                                <a href="{{ route('order.history') }}"
                                   class="nav-link px-4 py-2 {{ $currentRoute === 'order.history' ? 'active' : '' }}">
                                    My Orders
                                </a>
                            @endif

                            @if($role === 'vendor')
                                <a href="{{ route('vendor.menu') }}"
                                   class="nav-link px-4 py-2 {{ $currentRoute === 'vendor.menu' ? 'active' : '' }}">
                                    Menu Management
                                </a>
                            @endif

                            @if($role === 'staff')

                                <a href="{{ route('staff.vendors') }}"
                                   class="nav-link px-4 py-2 {{ $currentRoute === 'staff.vendors' ? 'active' : '' }}">
                                    Users
                                </a>
                            @endif

                            @if($role === 'student')
                                <a href="{{ route('feedback') }}"
                                   class="nav-link px-4 py-2 {{ $currentRoute === 'feedback' ? 'active' : '' }}">
                                    Feedback
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Right Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <!-- Livewire Notification Bell -->
                        <div class="notification-bell-wrapper">
                            @livewire('notification-bell')
                        </div>

                        <!-- User Menu -->
                        <div class="relative">
                            <button onclick="toggleDropdown()" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($role) }}</div>
                                </div>
                                <div class="avatar">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>

                            <div id="userDropdown" class="dropdown hidden">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    Profile Settings
                                </a>

                                <!-- Link to full notifications page -->
                                <a href="{{ route('notifications') }}" class="dropdown-item">
                                    <i class="fas fa-bell"></i>
                                    All Notifications
                                </a>



                                <div class="dropdown-divider"></div>
                                <button onclick="showLogoutModal()" class="dropdown-item w-full text-left text-red-600">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Log Out
                                </button>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">Log In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="md:hidden btn-ghost p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu md:hidden border-t border-gray-200">
            <div class="px-4 py-4 space-y-1 bg-gray-50">
                @php
                    $user = auth()->user();
                    $role = $user ? $user->role : null;
                @endphp

                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    Dashboard
                </a>

                @if(!$user || in_array($role, ['student', 'staff', null]))
                    <a href="{{ route('menu.browse') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                        <i class="fas fa-utensils w-5 mr-3"></i>
                        Menu
                    </a>
                @endif

                @auth
                    <!-- Notifications link in mobile menu -->
                    <a href="{{ route('notifications') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                        <i class="fas fa-bell w-5 mr-3"></i>
                        Notifications
                    </a>

                    @if($role === 'vendor')
                        <a href="{{ route('vendor.orders') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <i class="fas fa-concierge-bell w-5 mr-3"></i>
                            Orders
                        </a>
                        <a href="{{ route('vendor.menu') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <i class="fas fa-edit w-5 mr-3"></i>
                            Menu Management
                        </a>
                    @endif

                    @if($role === 'student')
                        <a href="{{ route('order.history') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <i class="fas fa-history w-5 mr-3"></i>
                            My Orders
                        </a>
                        <a href="{{ route('feedback') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <i class="fas fa-comment-dots w-5 mr-3"></i>
                            Feedback
                        </a>
                    @endif

                    @if($role === 'staff')
                        <a href="{{ route('staff.menu') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <i class="fas fa-users-cog w-5 mr-3"></i>
                            Staff Panel
                        </a>
                        <a href="{{ route('staff.vendors') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <i class="fas fa-users w-5 mr-3"></i>
                            Users
                        </a>
                    @endif

                    <div class="divider my-2"></div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-white rounded-lg transition-colors">
                        <i class="fas fa-user w-5 mr-3"></i>
                        Profile
                    </a>

                    <button onclick="showLogoutModal()" class="flex items-center w-full text-left px-4 py-3 text-red-600 hover:bg-white rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                        Log Out
                    </button>
                @else
                    <div class="px-4 py-3 space-y-2">
                        <a href="{{ route('login') }}" class="btn btn-secondary w-full">Log In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary w-full">Sign Up</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            @if (isset($header))
                <div class="page-header">
                    <h1 class="page-title">{{ $header }}</h1>
                </div>
            @endif

            <!-- Status Messages -->
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-3"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mt-2 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-3"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <!-- Page Content -->
            <div class="card p-6">
                {{ $slot }}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="logo-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <span class="logo-text">Smart Campus Canteen</span>
                    </div>
                    <p class="text-sm text-gray-600 max-w-md">
                        Revolutionizing campus dining with smart technology, reduced wait times, and delicious meals made just for you.
                    </p>
                </div>

                <div>
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('menu.browse') }}" class="footer-link">Browse Menu</a></li>
                        @auth
                            @if($role === 'student')
                                <li><a href="{{ route('order.history') }}" class="footer-link">My Orders</a></li>
                            @endif
                            @if($role === 'vendor')
                                <li><a href="{{ route('vendor.orders') }}" class="footer-link">Orders</a></li>
                                <li><a href="{{ route('vendor.menu') }}" class="footer-link">Menu Management</a></li>
                            @endif
                            <!-- Add notifications to footer links -->
                            <li><a href="{{ route('notifications') }}" class="footer-link">Notifications</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h3 class="footer-title">Contact</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt w-5 mt-0.5 mr-2"></i>
                            <span>Universiti Tun Hussein Onn Malaysia</span>
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone w-5 mr-2"></i>
                            <span>(+60) 16-787 8547</span>
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope w-5 mr-2"></i>
                            <span>info@smartcanteen.edu</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="divider mt-8 mb-6"></div>

            <div class="text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Smart Campus Canteen. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Confirm Logout</h3>
            <p class="text-sm text-gray-600 text-center mb-6">
                Are you sure you want to log out of your account?
            </p>

            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ route('dashboard') }}">
                <button type="submit" class="btn btn-primary w-full mb-2" style="background: var(--danger);">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Yes, Log Out
                </button>
            </form>

            <button onclick="hideLogoutModal()" class="btn btn-secondary w-full">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const button = document.querySelector('button[onclick="toggleDropdown()"]');

        if (button && !button.contains(event.target) && dropdown && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Mobile menu functionality
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('open');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.remove('open');
        }
    });

    // Logout Modal Functions
    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
        // Close user dropdown if open
        document.getElementById('userDropdown').classList.add('hidden');
        // Close mobile menu if open
        mobileMenu.classList.remove('open');
    }

    function hideLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('logoutModal');
        if (event.target === modal) {
            hideLogoutModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideLogoutModal();
        }
    });

    // Handle logout form submission
    document.getElementById('logoutForm').addEventListener('submit', function(e) {
        // Show loading state
        const logoutBtn = this.querySelector('.logout-btn');
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Logging out...';
        logoutBtn.disabled = true;
    });

    // Show toast notifications for order status updates
    // FIXED: Order status update event listener
    window.addEventListener('order-status-updated', (event) => {
        const detail = event.detail[0] || event.detail; // Handle both array and object formats

        // Extract values with proper fallbacks
        const orderId = detail.orderId || 'N/A';
        const orderNumber = detail.orderNumber || 'undefined';
        const status = detail.status || 'unknown';

        let title = 'Order Updated';
        let text = `Order #${orderNumber} has been ${status}`;
        let icon = 'info';

        // Customize message and icon based on status
        switch(status) {
            case 'confirmed':
                icon = 'success';
                text = `Order #${orderNumber} has been confirmed`;
                break;
            case 'preparing':
                icon = 'info';
                text = `Order #${orderNumber} is now being prepared`;
                break;
            case 'ready':
                icon = 'success';
                text = `Order #${orderNumber} is ready for pickup!`;
                break;
            case 'completed':
                icon = 'success';
                text = `Order #${orderNumber} has been completed`;
                break;
            case 'cancelled':
                icon = 'error';
                text = `Order #${orderNumber} has been cancelled`;
                break;
        }

        // Show SweetAlert toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'custom-swal'
            }
        });
    });

    // Add custom styles for SweetAlert
    const style = document.createElement('style');
    style.textContent = `
        .custom-swal {
            font-size: 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--gray-200);
        }
        .swal2-popup.swal2-toast {
            padding: 0.75rem;
        }
    `;
    document.head.appendChild(style);
</script>

@livewireScripts
</body>
</html>
