<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register - Smart Campus Canteen</title>

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

        .form-step {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }

        .form-step.active {
            display: block;
        }

        .step-indicator {
            position: relative;
            z-index: 1;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e5e7eb;
            z-index: -1;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            background-color: #f3f4f6;
            border: 2px solid #e5e7eb;
            color: #9ca3af;
            transition: all 0.3s ease;
        }

        .step-circle.active {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .step-circle.completed {
            background-color: #10b981;
            border-color: #10b981;
            color: white;
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: slideUp 0.3s ease-out;
            max-width: 400px;
            width: 90%;
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
    <!-- Register Container -->
    <div class="w-full max-w-2xl">
        <!-- Logo & Title -->
        <div class="text-center mb-10">
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center shadow-md">
                    <span class="text-2xl text-white">🍽️</span>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Join Smart Campus Canteen</h1>
            <p class="text-gray-600">Create your account in just a few steps</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-xl card-shadow p-8">
            <!-- Step Indicator -->
            <div class="mb-10">
                <div class="step-indicator flex justify-between items-center">
                    <div class="flex flex-col items-center">
                        <div class="step-circle active" data-step="1">
                            <span>1</span>
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-700">Basic Info</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="step-circle" data-step="2">
                            <span>2</span>
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-600">Account Type</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="step-circle" data-step="3">
                            <span>3</span>
                        </div>
                        <span class="mt-2 text-sm font-medium text-gray-600">Security</span>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" id="registration-form" class="space-y-6">
                @csrf

                <!-- Step 1: Basic Information -->
                <div id="step-1" class="form-step active">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                    placeholder="John Doe"
                                >
                            </div>
                            @error('name')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address *
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
                                    autocomplete="email"
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                    placeholder="john@example.com"
                                >
                            </div>
                            @error('email')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input
                                    id="phone_number"
                                    type="tel"
                                    name="phone_number"
                                    value="{{ old('phone_number') }}"
                                    autocomplete="tel"
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                    placeholder="+1 (555) 123-4567"
                                >
                            </div>
                            @error('phone_number')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="button" class="btn-primary px-6 py-3 rounded-lg font-medium" onclick="nextStep(2)">
                            Continue <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Account Type -->
                <div id="step-2" class="form-step">
                    <div class="space-y-6">
                        <!-- Role Selection -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                I am a *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tag text-gray-400"></i>
                                </div>
                                <select
                                    id="role"
                                    name="role"
                                    required
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 input-focus focus:outline-none appearance-none"
                                >
                                    <option value="" disabled selected>Select your role</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            @error('role')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Faculty (Conditional) -->
                        <div id="faculty-field" class="hidden space-y-4">
                            <div>
                                <label for="faculty" class="block text-sm font-medium text-gray-700 mb-2">
                                    Faculty/Department
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-graduation-cap text-gray-400"></i>
                                    </div>
                                    <input
                                        id="faculty"
                                        type="text"
                                        name="faculty"
                                        value="{{ old('faculty') }}"
                                        class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                        placeholder="Computer Science, Engineering, etc."
                                    >
                                </div>
                                @error('faculty')
                                <p class="text-red-600 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Matric Number (Conditional) -->
                        <div id="matric-field" class="hidden">
                            <label for="matric_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Matric Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input
                                    id="matric_number"
                                    type="text"
                                    name="matric_number"
                                    value="{{ old('matric_number') }}"
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                    placeholder="e.g., CS2023001"
                                >
                            </div>
                            @error('matric_number')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition duration-200" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="button" class="btn-primary px-6 py-3 rounded-lg font-medium" onclick="nextStep(3)">
                            Continue <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Security -->
                <div id="step-3" class="form-step">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                    placeholder="Create a strong password"
                                >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="password-strength" id="password-strength"></div>
                                <p class="text-xs text-gray-500 mt-1" id="password-hint">
                                    Use at least 8 characters with a mix of letters, numbers & symbols
                                </p>
                            </div>
                            @error('password')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-10 px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 input-focus focus:outline-none"
                                    placeholder="Confirm your password"
                                >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-center" id="req-length">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                At least 8 characters
                            </li>
                            <li class="flex items-center" id="req-uppercase">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                One uppercase letter
                            </li>
                            <li class="flex items-center" id="req-number">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                One number
                            </li>
                            <li class="flex items-center" id="req-symbol">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                One special character (optional)
                            </li>
                        </ul>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-start">
                            <input
                                id="terms"
                                type="checkbox"
                                name="terms"
                                required
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1"
                            >
                            <label for="terms" class="ml-2 block text-sm text-gray-700">
                                I agree to the <a href="#" class="font-medium text-blue-600 hover:text-blue-800">Terms of Service</a> and <a href="#" class="font-medium text-blue-600 hover:text-blue-800">Privacy Policy</a>
                            </label>
                        </div>
                        @error('terms')
                        <p class="text-red-600 text-sm mt-1 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition duration-200" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="button" class="btn-primary px-8 py-3 rounded-lg font-medium" onclick="validateAndSubmit()">
                            <i class="fas fa-user-plus mr-2"></i> Create Account
                        </button>
                    </div>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</div>

<!-- Password Strength Modal -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Weak Password Detected</h3>
                    <p class="text-sm text-gray-600" id="modal-subtitle">Your password strength is weak</p>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="mb-6">
                <p class="text-gray-700 mb-4" id="modal-message">
                    Your password is too weak and may be easily guessed. For your security, we recommend using a stronger password.
                </p>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-2">Password Strength:</p>
                    <div class="flex items-center mb-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full" id="modal-strength-bar"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium" id="modal-strength-text">Weak</span>
                    </div>
                    <p class="text-xs text-gray-600" id="modal-requirements"></p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancel
                </button>
                <div class="flex space-x-2">
                    <button type="button" onclick="improvePassword()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Improve Password
                    </button>
                    <button type="button" onclick="proceedAnyway()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                        Proceed Anyway
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Multi-step form functionality
    let currentStep = 1;
    const totalSteps = 3;
    let passwordStrength = 0; // 0: weak, 1: moderate, 2: strong, 3: very strong
    let isModalOpen = false;

    function nextStep(step) {
        if (step > currentStep && validateStep(currentStep)) {
            document.getElementById(`step-${currentStep}`).classList.remove('active');
            document.getElementById(`step-${step}`).classList.add('active');
            updateStepIndicator(currentStep, step);
            currentStep = step;
        }
    }

    function prevStep(step) {
        document.getElementById(`step-${currentStep}`).classList.remove('active');
        document.getElementById(`step-${step}`).classList.add('active');
        updateStepIndicator(currentStep, step, false);
        currentStep = step;
    }

    function updateStepIndicator(oldStep, newStep, forward = true) {
        const oldCircle = document.querySelector(`.step-circle[data-step="${oldStep}"]`);
        const newCircle = document.querySelector(`.step-circle[data-step="${newStep}"]`);

        if (forward) {
            oldCircle.classList.remove('active');
            oldCircle.classList.add('completed');
        } else {
            oldCircle.classList.remove('active');
            newCircle.classList.add('active');
            newCircle.classList.remove('completed');
        }

        if (!newCircle.classList.contains('completed')) {
            newCircle.classList.add('active');
        }
    }

    function validateStep(step) {
        const inputs = document.querySelectorAll(`#step-${step} [required]`);
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        return isValid;
    }

    // Role-based field visibility
    const roleSelect = document.getElementById('role');
    const facultyField = document.getElementById('faculty-field');
    const matricField = document.getElementById('matric-field');

    roleSelect.addEventListener('change', function() {
        const role = this.value;

        // Show/hide faculty field for students and staff
        if (role === 'student' || role === 'staff') {
            facultyField.classList.remove('hidden');
            facultyField.querySelector('input').required = true;
        } else {
            facultyField.classList.add('hidden');
            facultyField.querySelector('input').required = false;
        }

        // Show/hide matric field only for students
        if (role === 'student') {
            matricField.classList.remove('hidden');
            matricField.querySelector('input').required = true;
        } else {
            matricField.classList.add('hidden');
            matricField.querySelector('input').required = false;
        }
    });

    // Trigger change event on page load if there's a previously selected value
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }

    // Password strength calculation and UI update
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength');
    const passwordHint = document.getElementById('password-hint');
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqNumber = document.getElementById('req-number');
    const reqSymbol = document.getElementById('req-symbol');

    function checkPasswordRequirements(password) {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            symbol: /[^A-Za-z0-9]/.test(password)
        };

        return requirements;
    }

    function updateRequirementUI(requirements) {
        // Update requirement icons
        reqLength.querySelector('i').className = requirements.length ?
            'fas fa-check-circle text-green-500 mr-2 text-xs' :
            'fas fa-circle text-gray-300 mr-2 text-xs';

        reqUppercase.querySelector('i').className = requirements.uppercase ?
            'fas fa-check-circle text-green-500 mr-2 text-xs' :
            'fas fa-circle text-gray-300 mr-2 text-xs';

        reqNumber.querySelector('i').className = requirements.number ?
            'fas fa-check-circle text-green-500 mr-2 text-xs' :
            'fas fa-circle text-gray-300 mr-2 text-xs';

        reqSymbol.querySelector('i').className = requirements.symbol ?
            'fas fa-check-circle text-green-500 mr-2 text-xs' :
            'fas fa-circle text-gray-300 mr-2 text-xs';
    }

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const requirements = checkPasswordRequirements(password);

        // Calculate strength
        let strength = 0;
        if (requirements.length) strength++;
        if (requirements.uppercase) strength++;
        if (requirements.number) strength++;
        // Symbol is optional for moderate strength

        passwordStrength = strength; // Update global variable

        // Update requirement UI
        updateRequirementUI(requirements);

        // Update strength bar
        strengthBar.style.width = `${Math.min(strength * 25, 100)}%`;

        // Set color and hint based on strength
        let hint = '';
        if (strength === 0) {
            strengthBar.style.backgroundColor = '#dc2626';
            hint = 'Very weak password';
        } else if (strength === 1) {
            strengthBar.style.backgroundColor = '#ef4444';
            hint = 'Weak password';
        } else if (strength === 2) {
            strengthBar.style.backgroundColor = '#f59e0b';
            hint = 'Moderate password - OK to use';
        } else if (strength === 3) {
            strengthBar.style.backgroundColor = '#10b981';
            hint = 'Strong password';
        } else {
            strengthBar.style.backgroundColor = '#059669';
            hint = 'Very strong password';
        }

        passwordHint.textContent = hint;
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Modal functions
    function showModal() {
        const modal = document.getElementById('passwordModal');
        modal.classList.add('active');
        isModalOpen = true;
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('passwordModal');
        modal.classList.remove('active');
        isModalOpen = false;
        document.body.style.overflow = 'auto';
    }

    function validateAndSubmit() {
        const password = passwordInput.value;
        const requirements = checkPasswordRequirements(password);

        // Check if password is weak (only 1 requirement met or less)
        const metRequirements = Object.values(requirements).filter(Boolean).length;

        if (metRequirements <= 1) {
            // Show weak password modal
            showModal();

            // Update modal content
            document.getElementById('modal-title').textContent = 'Weak Password Detected';
            document.getElementById('modal-subtitle').textContent = 'Your password needs improvement';
            document.getElementById('modal-message').textContent = 'Your password is too weak and may be easily guessed. For better security, we recommend improving your password.';

            const strengthBar = document.getElementById('modal-strength-bar');
            strengthBar.style.width = '25%';
            strengthBar.style.backgroundColor = '#ef4444';
            strengthBar.style.height = '8px';
            strengthBar.style.borderRadius = '4px';

            document.getElementById('modal-strength-text').textContent = 'Weak';
            document.getElementById('modal-strength-text').className = 'ml-3 text-sm font-medium text-red-600';

            let missingReqs = [];
            if (!requirements.length) missingReqs.push('at least 8 characters');
            if (!requirements.uppercase) missingReqs.push('one uppercase letter');
            if (!requirements.number) missingReqs.push('one number');

            document.getElementById('modal-requirements').textContent =
                `Missing: ${missingReqs.join(', ')}`;

            return false;
        } else if (metRequirements === 2) {
            // Show moderate password warning but allow proceed
            showModal();

            // Update modal content for moderate password
            document.getElementById('modal-title').textContent = 'Moderate Password Warning';
            document.getElementById('modal-subtitle').textContent = 'Your password strength is moderate';
            document.getElementById('modal-message').textContent = 'Your password is moderately strong. You can proceed with registration, but consider adding more complexity for better security.';

            const strengthBar = document.getElementById('modal-strength-bar');
            strengthBar.style.width = '50%';
            strengthBar.style.backgroundColor = '#f59e0b';
            strengthBar.style.height = '8px';
            strengthBar.style.borderRadius = '4px';

            document.getElementById('modal-strength-text').textContent = 'Moderate';
            document.getElementById('modal-strength-text').className = 'ml-3 text-sm font-medium text-yellow-600';

            let suggestions = [];
            if (!requirements.symbol) suggestions.push('Add a special character (!@#$%^&*)');

            document.getElementById('modal-requirements').textContent =
                suggestions.length ? `Suggestion: ${suggestions.join(', ')}` : 'All basic requirements met';

            return false;
        } else {
            // Strong password - submit directly
            document.getElementById('registration-form').submit();
            return true;
        }
    }

    function proceedAnyway() {
        closeModal();
        document.getElementById('registration-form').submit();
    }

    function improvePassword() {
        closeModal();
        passwordInput.focus();

        // Show password requirements
        const requirementsSection = document.querySelector('.bg-gray-50');
        requirementsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Highlight the requirements section
        requirementsSection.classList.add('ring-2', 'ring-blue-300');
        setTimeout(() => {
            requirementsSection.classList.remove('ring-2', 'ring-blue-300');
        }, 2000);
    }

    // Close modal when clicking outside
    document.getElementById('passwordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isModalOpen) {
            closeModal();
        }
    });

    // Initialize password strength indicator
    passwordInput.dispatchEvent(new Event('input'));
</script>
</body>
</html>
