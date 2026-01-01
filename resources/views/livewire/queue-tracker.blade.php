<div>
    <!-- Debug Information (Remove in production) -->
    <!-- Debug Information (Remove in production) -->
    @if($canteenId && config('app.debug'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h4 class="font-bold text-red-800 mb-2">🔍 DEBUG INFORMATION</h4>
            <div class="text-sm space-y-1">
                @php
                    $debugInfo = $this->debugOrders();
                @endphp
                <p><strong>Canteen ID:</strong> {{ $canteenId }}</p>
                <p><strong>Meal IDs:</strong> {{ json_encode($debugInfo['meal_ids'] ?? []) }}</p>
                <p><strong>Debug Query Orders Found:</strong> {{ json_encode($debugInfo['debug_orders_found'] ?? []) }} (Count: {{ $debugInfo['debug_orders_count'] ?? 0 }})</p>
                <p><strong>Current Component Orders:</strong> {{ json_encode($debugInfo['current_component_orders'] ?? []) }} (Count: {{ $debugInfo['current_component_orders_count'] ?? 0 }})</p>
                <p><strong>Orders Count in View:</strong> {{ count($orders) }}</p>
                <p><strong>Queue Stats:</strong> {{ json_encode($queueStats) }}</p>
                <p class="mt-2 text-xs bg-white p-2 rounded border">
                    <strong>Check Laravel logs for detailed execution flow</strong>
                </p>
            </div>
        </div>
    @endif

    <!-- Canteen Selector -->
    @if(!$canteenId)
        <div class="mb-6">
            <label for="canteenSelect" class="block text-sm font-medium text-gray-700 mb-2">
                Select Canteen
            </label>
            <select wire:model.live="canteenId" id="canteenSelect"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Canteens Overview</option>
                @foreach($canteens as $canteen)
                    <option value="{{ $canteen->id }}">{{ $canteen->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- All Canteens Overview -->
    @if(!$canteenId)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($queueStats as $canteenId => $stats)
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4
                          {{ $stats['is_open'] ? 'border-green-500' : 'border-red-500' }}">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold">{{ $stats['name'] }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                   {{ $stats['is_open'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $stats['is_open'] ? 'OPEN' : 'CLOSED' }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Queue Length:</span>
                            <span class="font-semibold">{{ $stats['queue_count'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Est. Wait Time:</span>
                            <span class="font-semibold">{{ $stats['wait_time'] }} min</span>
                        </div>
                    </div>

                    <button wire:click="$set('canteenId', {{ $canteenId }})"
                            class="w-full mt-4 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        View Queue Details
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <!-- Single Canteen Queue Details -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-6">
                <div class="flex justify-between items-center">
                    <div>
                        @php
                            $currentCanteen = $canteens->find($canteenId);
                        @endphp
                        <h2 class="text-2xl font-bold">{{ $currentCanteen->name ?? 'Unknown Canteen' }}</h2>
                        @if($currentCanteen && $currentCanteen->location)
                            <p class="text-blue-100">{{ $currentCanteen->location }}</p>
                        @endif
                    </div>
                    <button wire:click="$set('canteenId', null)"
                            class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded-md transition-colors">
                        ← Back to Overview
                    </button>
                </div>
            </div>

            <!-- Queue Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-gray-50">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $queueStats['total_in_queue'] ?? 0 }}</div>
                    <div class="text-gray-600">Total in Queue</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $queueStats['estimated_wait_time'] ?? 0 }} min</div>
                    <div class="text-gray-600">Max Wait Time</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $queueStats['current_serving'] ?? 0 }}</div>
                    <div class="text-gray-600">Now Serving</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $queueStats['orders_ready'] ?? 0 }}</div>
                    <div class="text-gray-600">Ready Now</div>
                </div>
            </div>

            <!-- Orders Queue -->
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Order Queue</h3>
                    <div class="text-sm text-gray-500">
                        Updated: {{ now()->format('h:i A') }}
                    </div>
                </div>

                @if(count($orders) > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            @php
                                $timeEstimate = $this->getTimeEstimate($order->id);
                            @endphp
                            <div class="border rounded-lg p-4 transition-all duration-300
                                      {{ $order->status === 'ready' ? 'bg-green-50 border-green-200 shadow-sm' :
                                         ($order->status === 'preparing' ? 'bg-yellow-50 border-yellow-200' : 'bg-white border-gray-200') }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="font-mono font-bold text-lg bg-gray-100 px-2 py-1 rounded">
                                                #{{ $order->queue_number ?? $order->id }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                       {{ $order->status === 'ready' ? 'bg-green-100 text-green-800' :
                                                          ($order->status === 'preparing' ? 'bg-yellow-100 text-yellow-800' :
                                                          ($order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <span class="text-sm font-medium
                                                      {{ $timeEstimate['minutes_left'] <= 5 ? 'text-green-600' :
                                                         ($timeEstimate['minutes_left'] <= 15 ? 'text-orange-600' : 'text-gray-600') }}">
                                                ⏱ {{ $timeEstimate['message'] }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $order->meal->name ?? 'Unknown Meal' }}</p>
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
                                    </div>

                                    <div class="text-right ml-4">
                                        <p class="text-lg font-bold text-green-600">RM{{ number_format($order->total_amount, 2) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Prep: {{ $order->meal->preparation_time ?? 10 }}min
                                        </p>
                                    </div>
                                </div>

                                <!-- Progress indicator for preparing orders -->
                                @if($order->status === 'preparing')
                                    <div class="mt-3">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Preparing...</span>
                                            <span>{{ max(1, $timeEstimate['minutes_left']) }} min left</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-500 h-2 rounded-full animate-pulse"
                                                 style="width: 60%"></div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons (for vendors) -->
                                @auth
                                    @if(auth()->user()->role === 'vendor' || auth()->user()->role === 'admin')
                                        <div class="mt-3 pt-3 border-t border-gray-200 flex gap-2 flex-wrap">
                                            @if($order->status === 'pending' || $order->status === 'confirmed')
                                                <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')"
                                                        class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition-colors">
                                                    Start Preparing
                                                </button>
                                            @endif
                                            @if($order->status === 'preparing')
                                                <button wire:click="updateOrderStatus({{ $order->id }}, 'ready')"
                                                        class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition-colors">
                                                    Mark Ready
                                                </button>
                                            @endif
                                            @if($order->status === 'ready')
                                                <button wire:click="updateOrderStatus({{ $order->id }}, 'completed')"
                                                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition-colors">
                                                    Complete Order
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-lg mb-2">No orders in queue</p>
                        <p class="text-sm">New orders will appear here automatically</p>

                        <!-- Debug help -->
                        @if(config('app.debug'))
                            <div class="mt-4 p-3 bg-gray-100 rounded-lg text-left">
                                <p class="text-xs font-mono">
                                    <strong>Debug Info:</strong><br>
                                    Canteen ID: {{ $canteenId }}<br>
                                    Check Laravel logs for detailed query information
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Auto-refresh notification -->
        <div class="mt-4 text-center">
            <div class="inline-flex items-center text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                Queue updates automatically every 30 seconds
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            {{ session('message') }}
        </div>
    @endif
</div>

<script>
    // Auto-refresh the queue every 30 seconds
    document.addEventListener('livewire:init', () => {
        setInterval(() => {
            Livewire.dispatch('refreshQueue');
        }, 30000);
    });
</script>
