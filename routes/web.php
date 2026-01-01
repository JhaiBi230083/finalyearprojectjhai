<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\Livewire\MenuBrowser;
use App\Livewire\Notifications;
use App\Livewire\OrderDetail;
use App\Livewire\OrderPlacer;
use App\Livewire\QueueTracker;
use App\Livewire\OrderHistory;
use App\Livewire\FeedbackSystem;
use App\Livewire\StaffVendorManagement;
use App\Livewire\StudentQueue;
use App\Livewire\VendorMenuManagement;
use App\Livewire\StaffMenuManagement;
use App\Livewire\Dashboard;
use App\Livewire\VendorOrdersManagement;
use Illuminate\Support\Facades\Route;

// Dashboard as welcome page
Route::get('/dashboard', Dashboard::class)->name('dashboard');

// Public routes
Route::get('/menu', MenuBrowser::class)->name('menu.browse');
Route::get('/queue', QueueTracker::class)->name('queue.tracker');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Order routes
    Route::get('/order/{mealId}', OrderPlacer::class)->name('order.place');
    Route::get('/orders/{orderId}', OrderDetail::class)->name('order.details');
    Route::get('/orders', OrderHistory::class)->name('order.history');

    // Feedback routes
    Route::get('/feedback', FeedbackSystem::class)->name('feedback');
    Route::get('/queue/student', StudentQueue::class)->name('student.queue');
    // Authentication
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/staff/vendors', StaffVendorManagement::class)->name('staff.vendors');
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    // Vendor routes

        Route::get('/vendor', VendorMenuManagement::class)->name('vendor.menu');

// In the vendor middleware group

        Route::get('/vendor', VendorMenuManagement::class)->name('vendor.menu');
        Route::get('/vendor/orders', VendorOrdersManagement::class)->name('vendor.orders');

    // Staff routes

        Route::get('/staff/menu', StaffMenuManagement::class)->name('staff.menu');

});
Route::middleware(['auth'])->group(function () {
    // Notifications page
    Route::get('/notifications', Notifications::class)->name('notifications');

    // Add notification bell to layout
    // This is typically included in your app layout
});
