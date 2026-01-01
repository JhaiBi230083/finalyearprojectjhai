<div class="relative" x-data="{ open: {{ $showDropdown ? 'true' : 'false' }} }" @click.outside="open = false">
    <!-- Bell Icon -->
    <button @click="open = !open"
            class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-5 w-5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white font-bold">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 md:w-96 bg-white rounded-lg shadow-lg border z-50 max-h-96 overflow-y-auto">

        <!-- Header -->
        <div class="p-4 border-b">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Notifications</h3>
                <div class="flex items-center space-x-2">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead"
                                wire:confirm="Mark all notifications as read?"
                                class="text-sm text-blue-600 hover:text-blue-800">
                            Mark all as read
                        </button>
                    @endif
                    <button wire:click="goToNotificationsPage"
                            class="text-sm text-gray-600 hover:text-gray-900">
                        View all
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="divide-y">
            @if(count($recentNotifications) > 0)
                @foreach($recentNotifications as $notification)
                    <div wire:key="bell-notification-{{ $notification->id }}"
                         class="p-4 hover:bg-gray-50 cursor-pointer {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                         wire:click="markAsRead({{ $notification->id }})">
                        <div class="flex items-start space-x-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-1">
                                    <p class="text-sm font-medium text-gray-900 truncate {{ !$notification->is_read ? 'text-blue-800' : '' }}">
                                        {{ $notification->title }}
                                    </p>
                                    <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">
                                    {{ $notification->message }}
                                </p>
                                @if(!$notification->is_read)
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mt-1"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-4 text-center text-gray-500">
                    No notifications
                </div>
            @endif
        </div>
    </div>
</div>
