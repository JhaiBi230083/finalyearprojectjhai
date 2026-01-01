<div>
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-1">
                You have {{ $unreadCount }} unread notifications
            </p>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div wire:key="notification-{{ $notification->id }}"
                         class="border rounded-lg p-4 transition-all duration-200 hover:shadow-md {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }} {{ $this->getNotificationColor($notification->type) }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3 flex-1">
                                <!-- Icon -->
                                <div class="text-xl">
                                    {{ $this->getNotificationIcon($notification->type) }}
                                </div>

                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="font-semibold text-gray-900 {{ !$notification->is_read ? 'text-blue-800' : '' }}">
                                            {{ $notification->title }}
                                        </h3>
                                        <span class="text-sm text-gray-500">
                                            {{ $this->timeAgo($notification->created_at) }}
                                        </span>
                                    </div>

                                    <p class="text-gray-700 mb-2">
                                        {{ $notification->message }}
                                    </p>

                                    <!-- Order Info -->
                                    @if($notification->order_id && $notification->order)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="font-medium">Order #{{ $notification->order->order_number }}</span>
                                            @if($notification->order->meal)
                                                <span class="mx-2">•</span>
                                                <span>{{ $notification->order->meal->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Mark as Read Action -->
                            <div class="flex items-center ml-4">
                                @if(!$notification->is_read)
                                    <button wire:click="markAsRead({{ $notification->id }})"
                                            class="p-2 text-blue-600 hover:bg-blue-100 rounded-full transition"
                                            title="Mark as read">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    No notifications yet
                </h3>
                <p class="text-gray-600">
                    Notifications about your orders will appear here.
                </p>
            </div>
        @endif
    </div>
</div>
