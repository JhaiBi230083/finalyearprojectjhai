<div>
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Loading State -->
        @if($loading)
            <div class="p-8 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading order details...</p>
            </div>
            <!-- Not Found State -->
        @elseif($notFound || !$order)
            <div class="p-8 text-center">
                <div class="text-gray-400 text-6xl mb-4">📦</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Order Not Found</h3>
                <p class="text-gray-500 mb-6">The order you're looking for doesn't exist or you don't have permission to view it.</p>
                <div class="flex gap-3 justify-center">
                    <a href="{{ route('order.history') }}"
                       class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
                        View Order History
                    </a>
                    <a href="{{ route('menu.browse') }}"
                       class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-colors">
                        Browse Menu
                    </a>
                </div>
            </div>
        @else
            <!-- Order Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Order #{{ $order->order_number }}</h1>
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-medium">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('M j, Y g:i A') }}
                            </span>
                            <!-- FIXED: Use component methods with $this -->
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $this->getStatusColor($order->status) }}">
                                {{ $this->getStatusIcon($order->status) }}
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->queue_number)
                                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-medium">
                                    Queue #{{ $order->queue_number }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        @if(in_array($order->status, ['pending', 'confirmed']))
                            <button wire:click="cancelOrder"
                                    wire:confirm="Are you sure you want to cancel this order?"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors text-sm font-medium">
                                Cancel Order
                            </button>
                        @endif
                        <a href="{{ route('order.history') }}"
                           class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-md transition-colors text-sm font-medium">
                            Back to History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session()->has('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Meal Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Meal Details</h2>
                        <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                            @if($order->meal->image_url)
                                <!-- Updated Image Container with better sizing -->
                                <div class="flex-shrink-0">
                                    <div class="relative w-40 h-40 sm:w-48 sm:h-48 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                        <img
                                            class="w-full h-full object-cover"
                                            src="{{ $order->meal->image_url }}"
                                            alt="{{ $order->meal->name }}"
                                            onerror="this.src='{{ asset('images/default-meal.jpg') }}'"
                                        >
                                    </div>

                                </div>
                            @else
                                <!-- Updated placeholder with better sizing -->
                                <div class="flex-shrink-0">
                                    <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-lg bg-gray-100 border border-gray-200 flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-500 mt-2">No image available</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900">{{ $order->meal->name }}</h3>
                                <p class="text-gray-600 mt-2">{{ $order->meal->description }}</p>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-500 block mb-1">Canteen:</span>
                                        <p class="font-medium text-gray-900">{{ $order->meal->canteen->name }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-500 block mb-1">Category:</span>
                                        <p class="font-medium text-gray-900">{{ $order->meal->category }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-500 block mb-1">Price per item:</span>
                                        <p class="font-medium text-green-600 text-lg">RM{{ number_format($order->meal->price, 2) }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-500 block mb-1">Quantity:</span>
                                        <p class="font-medium text-blue-600 text-lg">{{ $order->quantity }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Timeline</h2>
                        <div class="space-y-4">
                            @php
                                $timeline = [
                                    'pending' => ['icon' => '⏳', 'text' => 'Order placed', 'time' => $order->created_at],
                                    'confirmed' => ['icon' => '✅', 'text' => 'Order confirmed', 'time' => $order->created_at->addMinutes(1)],
                                    'preparing' => ['icon' => '👨‍🍳', 'text' => 'Preparing your meal', 'time' => $order->created_at->addMinutes(5)],
                                    'ready' => ['icon' => '📦', 'text' => 'Ready for pickup', 'time' => $this->getEstimatedReadyTime()],
                                    'completed' => ['icon' => '🎉', 'text' => 'Order completed', 'time' => $order->updated_at],
                                ];

                                $currentStatusIndex = array_keys($timeline);
                                $currentStatusPos = array_search($order->status, $currentStatusIndex);
                            @endphp

                            @foreach($timeline as $status => $info)
                                @php
                                    $isCompleted = array_search($status, $currentStatusIndex) <= $currentStatusPos;
                                    $isCurrent = $status === $order->status;
                                @endphp
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                    {{ $isCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                    {{ $isCurrent ? 'ring-2 ring-green-500 ring-offset-2' : '' }}">
                                            <span class="text-lg">{{ $info['icon'] }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 pt-2">
                                        <p class="font-medium {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $info['text'] }}
                                        </p>
                                        @if($info['time'])
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $info['time']->format('M j, g:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-10 flex justify-center">
                                            <div class="w-0.5 h-6 {{ $isCompleted ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    @if($order->special_instructions)
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Special Instructions</h2>
                            <p class="text-gray-700">{{ $order->special_instructions }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Summary & Actions -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Meal Price</span>
                                <span class="font-medium">RM{{ number_format($order->meal->price, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Quantity</span>
                                <span class="font-medium">{{ $order->quantity }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold pt-3 mt-2">
                                <span>Total Amount</span>
                                <span class="text-green-600">RM{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pickup Information</h2>
                        <div class="space-y-3">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <span class="text-blue-600 text-sm font-medium block mb-1">Preferred Pickup Time</span>
                                <p class="font-semibold text-gray-900">{{ $order->pickup_time->format('M j, g:i A') }}</p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <span class="text-yellow-600 text-sm font-medium block mb-1">Estimated Wait Time</span>
                                <p class="font-semibold text-gray-900">{{ $order->estimated_wait_time }} minutes</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <span class="text-green-600 text-sm font-medium block mb-1">Canteen Location</span>
                                <p class="font-semibold text-gray-900">{{ $order->meal->canteen->name }}</p>
                                @if($order->meal->canteen->location)
                                    <p class="text-sm text-gray-600 mt-1">{{ $order->meal->canteen->location }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    @if($order->payment)
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                            <div class="space-y-3">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <span class="text-gray-500 text-sm block mb-1">Payment Method</span>
                                    <p class="font-medium capitalize text-gray-900">{{ str_replace('_', ' ', $order->payment->payment_method) }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <span class="text-gray-500 text-sm block mb-1">Transaction ID</span>
                                    <p class="font-medium font-mono text-sm text-gray-900">{{ $order->payment->transaction_id }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <span class="text-gray-500 text-sm block mb-1">Payment Status</span>
                                    <span class="px-3 py-1.5 rounded-full text-sm font-medium
                                              {{ $order->payment->status === 'success' ? 'bg-green-100 text-green-800' :
                                                 ($order->payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                        <div class="space-y-3">
                            <a href="{{ route('menu.browse') }}"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-md transition-colors text-center block font-medium flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Order Again
                            </a>
                            @if(!$order->feedback)
                                <a href="{{ route('feedback', $order->id) }}"
                                   class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md transition-colors text-center block font-medium flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Leave Feedback
                                </a>
                            @else
                                <button disabled
                                        class="w-full bg-gray-400 text-white py-3 px-4 rounded-md text-center block font-medium cursor-not-allowed flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Feedback Submitted
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
