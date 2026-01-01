<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::listen('component.dehydrate', function ($component, $response) {
            if (method_exists($component, 'getEventQueue')) {
                $events = $component->getEventQueue();

                // Listen for order status updates
                if (isset($events['order-status-updated'])) {
                    // You can broadcast this event to other users
                    // This requires Laravel Echo and WebSockets setup
                }
            }
        });
        // Register custom Blade components
        Blade::component('components.nav-link', 'nav-link');
    }
}
