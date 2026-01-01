<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Queue Status</h1>
            <p class="text-gray-600 mt-2">Track your orders and see current queue lengths</p>
        </div>

        <!-- Canteen Selector -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Select Canteen</label>
            <div class="flex flex-wrap gap-3">
                @foreach($canteens as $canteen)
                    <button wire:click="$set('selectedCanteenId', {{ $canteen->id }})"
                            class="px-4 py-2 rounded-lg border-2 transition-all duration-200
                                   {{ $selectedCanteenId == $canteen->id
                                      ? 'border-blue-500 bg-blue-50 text-blue-700 font-medium'
                                      : 'border-gray-200 hover:border-gray-300 text-gray-700' }}">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🏪</span>
                            <span>{{ $canteen->name }}</span>
                            @if($canteen->isOpen())
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            @else
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        @if($loading)
            <!-- Loading State -->
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading queue data...</p>
            </div>
        @elseif(!$selectedCanteenId)
            <!-- No Canteen Selected -->
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <div class="text-6xl mb-4">🏪</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Canteen</h3>
                <p class="text-gray-500">Choose a canteen above to view its queue status</p>
            </div>
        @else
            <!-- Queue Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Orders Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <span class="text-blue-600 text-xl">📊</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total in Queue</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $queueStats['total_orders'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Waiting Orders Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <span class="text-yellow-600 text-xl">⏳</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Waiting</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $queueStats['pending_orders'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Preparing Orders Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="bg-orange-100 p-3 rounded-full">
                            <span class="text-orange-600 text-xl">👨‍🍳</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Preparing</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $queueStats['preparing_orders'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Estimated Wait Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <span class="text-green-600 text-xl">⏰</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Est. Wait Time</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $queueStats['estimated_wait_time'] ?? 0 }} min</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- My Orders Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <span class="text-blue-600">📋</span>
                                My Orders in Queue
                                @if(count($myOrders) > 0)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ count($myOrders) }}
                                    </span>
                                @endif
                            </h2>
                        </div>

                        <div class="p-6">
                            @if(count($myOrders) > 0)
                                <div class="space-y-4">
                                    @foreach($myOrders as $order)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200
                                                    {{ $order->status === 'ready' ? 'bg-green-50 border-green-200' : '' }}">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <span class="font-mono font-bold text-lg bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                            #{{ $order->queue_number ?? $order->id }}
                                                        </span>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            {{ $order->status === 'ready' ? 'bg-green-100 text-green-800' :
                                                               ($order->status === 'preparing' ? 'bg-orange-100 text-orange-800' :
                                                               'bg-yellow-100 text-yellow-800') }}">
                                                            @if($order->status === 'ready') 📦
                                                            @elseif($order->status === 'preparing') 👨‍🍳
                                                            @else ⏳
                                                            @endif
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                        <div>
                                                            <p class="font-medium text-gray-900">{{ $order->meal->name }}</p>
                                                            <p class="text-sm text-gray-600">Qty: {{ $order->quantity }}</p>
                                                            @if($order->special_instructions)
                                                                <p class="text-sm text-gray-500 mt-1">
                                                                    📝 {{ Str::limit($order->special_instructions, 50) }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-600">
                                                            <p>Order: {{ $order->order_number }}</p>
                                                            <p>Placed: {{ $order->created_at->format('h:i A') }}</p>
                                                        </div>
                                                    </div>

                                                    <!-- Progress Info -->
                                                    <div class="mt-3">
                                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                            <span>Queue Position: <strong>#{{ $this->getQueuePosition($order) }}</strong></span>
                                                            <span class="font-semibold text-blue-600">
                                                                {{ $this->getEstimatedReadyTime($order) }}
                                                            </span>
                                                        </div>
                                                        @if($order->status === 'preparing')
                                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                                <div class="bg-orange-500 h-2 rounded-full animate-pulse" style="width: 60%"></div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="text-right ml-4">
                                                    <p class="text-lg font-bold text-green-600">RM{{ number_format($order->total_amount, 2) }}</p>
                                                    <a href="{{ route('order.details', $order->id) }}"
                                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                                        View Details →
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-4">📭</div>
                                    <p class="text-gray-500">You have no active orders in this canteen</p>
                                    <a href="{{ route('menu.browse') }}"
                                       class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Order Now
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Queue Overview Section -->
                <div class="space-y-6">
                    <!-- Current Queue Stats -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <span class="text-green-600">📈</span>
                                Queue Overview
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Canteen Status</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $queueStats['is_open'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $queueStats['is_open'] ? 'Open' : 'Closed' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Now Serving</span>
                                    <span class="font-mono font-bold text-blue-600">{{ $queueStats['current_serving'] ?? 'None' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Orders</span>
                                    <span class="font-semibold">{{ $queueStats['total_orders'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Estimated Wait</span>
                                    <span class="font-semibold text-orange-600">{{ $queueStats['estimated_wait_time'] ?? 0 }} min</span>
                                </div>
                            </div>

                            <!-- Queue Breakdown -->
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Queue Breakdown</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">🟡 Waiting</span>
                                        <span>{{ $queueStats['pending_orders'] ?? 0 }} orders</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">🟠 Preparing</span>
                                        <span>{{ $queueStats['preparing_orders'] ?? 0 }} orders</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">🟢 Ready</span>
                                        <span>{{ $queueStats['ready_orders'] ?? 0 }} orders</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('menu.browse') }}"
                                   class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block font-medium">
                                    🍽️ Order Food
                                </a>
                                <a href="{{ route('order.history') }}"
                                   class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-center block font-medium">
                                    📋 View All Orders
                                </a>
                                <button wire:click="refreshQueue"
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block font-medium">
                                    🔄 Refresh Queue
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Status -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-blue-800">Live Updates</span>
                        </div>
                        <p class="text-xs text-blue-600 mt-1">Queue updates automatically every 30 seconds</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    // Auto-refresh queue every 30 seconds
    document.addEventListener('livewire:init', () => {
        setInterval(() => {
        @this.refreshQueue();
        }, 30000);
    });

    // Add smooth scrolling for better UX
    document.addEventListener('livewire:navigated', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
@endscript
