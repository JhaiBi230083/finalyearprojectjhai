<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Loading State -->
        @if($loading)
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading order details...</p>
            </div>
        @endif

        <!-- Not Found State -->
        @if($notFound)
            <div class="text-center py-12">
                <div class="text-6xl mb-4">😕</div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Order Not Found</h2>
                <p class="text-gray-600 mb-6">We couldn't find the order you're looking for.</p>
                <a href="{{ route('order.history') }}"
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    View Order History
                </a>
            </div>
        @endif

        @if($order && !$loading)
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                        <p class="text-gray-600 mt-1">{{ $order->meal->name }} • {{ $order->meal->canteen->name }}</p>
                        <p class="text-sm text-gray-500 mt-2">Placed on {{ $order->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getStatusColorProperty() }}">
                            {{ $this->getStatusIconProperty() }} {{ ucfirst($order->status) }}
                        </span>
                        <p class="text-lg font-bold text-green-600 mt-2">RM{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Order Progress</h3>
                    <span class="text-sm font-medium text-blue-600">{{ round($this->getProgressPercentage()) }}% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500 ease-in-out"
                         style="width: {{ $this->getProgressPercentage() }}%"></div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Status Timeline</h3>

                <div class="space-y-8">
                    @foreach($statusTimeline as $index => $step)
                        <div class="flex items-start space-x-4">
                            <!-- Timeline line -->
                            @if($index > 0)
                                <div class="absolute left-7 top-0 bottom-0 w-0.5 bg-gray-200 -ml-0.5"></div>
                            @endif

                            <!-- Status icon -->
                            <div class="relative flex-shrink-0">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl
                                    {{ $step['completed'] ? 'bg-green-100 text-green-600 border-2 border-green-200' :
                                       ($step['current'] ? 'bg-blue-100 text-blue-600 border-2 border-blue-200 animate-pulse' :
                                       'bg-gray-100 text-gray-400 border-2 border-gray-200') }}">
                                    {{ $step['icon'] }}
                                </div>

                                @if($step['completed'] && $index < count($statusTimeline) - 1)
                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    </div>
                                @endif
                            </div>

                            <!-- Status content -->
                            <div class="flex-1 min-w-0 pt-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 {{ $step['completed'] ? 'text-green-700' : ($step['current'] ? 'text-blue-700' : 'text-gray-500') }}">
                                            {{ $step['title'] }}
                                        </h4>
                                        <p class="text-gray-600 mt-1">{{ $step['description'] }}</p>
                                    </div>

                                    @if($step['time'])
                                        <div class="text-right text-sm text-gray-500 whitespace-nowrap ml-4">
                                            {{ $step['time']->format('M j, g:i A') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Additional info for current step -->
                                @if($step['current'])
                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        @if($step['status'] === 'preparing')
                                            <div class="flex items-center text-blue-700">
                                                <div class="animate-pulse mr-2">👨‍🍳</div>
                                                <span class="font-medium">Chef is actively preparing your meal</span>
                                            </div>
                                            <p class="text-sm text-blue-600 mt-1">
                                                Estimated ready time:
                                                <strong>{{ $this->getEstimatedReadyTime() ? $this->getEstimatedReadyTime()->format('g:i A') : 'Calculating...' }}</strong>
                                            </p>
                                        @elseif($step['status'] === 'ready')
                                            <div class="flex items-center text-green-700">
                                                <div class="mr-2">📦</div>
                                                <span class="font-medium">Your order is waiting for pickup</span>
                                            </div>
                                            <p class="text-sm text-green-600 mt-1">
                                                Please collect from: <strong>{{ $order->meal->canteen->location ?? $order->meal->canteen->name }}</strong>
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Meal Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-600">Meal:</span> {{ $order->meal->name }}</p>
                            <p><span class="text-gray-600">Quantity:</span> {{ $order->quantity }}</p>
                            <p><span class="text-gray-600">Canteen:</span> {{ $order->meal->canteen->name }}</p>
                            <p><span class="text-gray-600">Location:</span> {{ $order->meal->canteen->location ?? 'Main Counter' }}</p>
                            @if($order->special_instructions)
                                <p><span class="text-gray-600">Special Instructions:</span> {{ $order->special_instructions }}</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Pickup Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-600">Pickup Time:</span> {{ $order->pickup_time->format('M j, g:i A') }}</p>
                            <p><span class="text-gray-600">Estimated Wait:</span> {{ $order->estimated_wait_time ?? 15 }} minutes</p>
                            <p><span class="text-gray-600">Queue Number:</span>
                                <span class="font-mono font-bold text-blue-600">{{ $order->queue_number ?? 'Pending' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('order.history') }}"
                   class="text-blue-600 hover:text-blue-700 font-medium">
                    ← Back to Orders
                </a>

                @if(in_array($order->status, ['pending', 'confirmed']))
                    <button wire:click="cancelOrder"
                            wire:confirm="Are you sure you want to cancel this order?"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Cancel Order
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Auto-refresh script -->
<script>
    document.addEventListener('livewire:init', () => {
        // Auto-refresh every 30 seconds for active orders
        setInterval(() => {
        @this.checkForUpdates();
        }, 30000);
    });
</script>
