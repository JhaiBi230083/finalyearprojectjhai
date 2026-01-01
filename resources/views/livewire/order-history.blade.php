<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            <p class="text-gray-600 mt-2">View your order history and track current orders</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                        <select wire:model.live="filterStatus"
                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search by meal name..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 w-full md:w-64">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">{{ $orders->total() }} orders found</span>
                    <button wire:click="clearFilters"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($orders->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <!-- Clickable Order Card -->
                        <div wire:click="viewOrderDetails({{ $order->id }})"
                             class="p-6 hover:bg-gray-50 cursor-pointer transition-colors duration-200 order-card">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Order Info -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        @if($order->meal->image_url)
                                            <div class="flex-shrink-0">
                                                <img src="{{ $order->meal->image_url }}"
                                                     alt="{{ $order->meal->name }}"
                                                     class="w-16 h-16 rounded-lg object-cover">
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $order->meal->name }}
                                                </h3>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($order->status) }}">
                                                    {{ $this->getStatusIcon($order->status) }}
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <span class="text-gray-400">🏪</span>
                                                    <span>{{ $order->meal->canteen->name }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-gray-400">📅</span>
                                                    <span>{{ $order->created_at->format('M j, Y') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-gray-400">⏰</span>
                                                    <span>{{ $order->created_at->format('g:i A') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-gray-400">🔢</span>
                                                    <span>Qty: {{ $order->quantity }}</span>
                                                </div>
                                            </div>

                                            @if($order->queue_number)
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Queue #{{ $order->queue_number }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if($order->special_instructions)
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500">
                                                        <span class="font-medium">Note:</span>
                                                        {{ Str::limit($order->special_instructions, 100) }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Actions & Total -->
                                <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end gap-3">
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-green-600">
                                            RM{{ number_format($order->total_amount, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Order #{{ $order->order_number }}
                                        </p>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="flex gap-2">
                                        @if(in_array($order->status, ['pending', 'confirmed']))
                                            <button wire:click.stop="cancelOrder({{ $order->id }})"
                                                    wire:confirm="Are you sure you want to cancel this order?"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1 border border-red-300 rounded hover:bg-red-50 transition-colors">
                                                Cancel
                                            </button>
                                        @endif
                                        <button wire:click.stop="viewOrderDetails({{ $order->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-1 border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Click hint for mobile -->
                            <div class="mt-3 flex items-center justify-center lg:hidden">
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                    </svg>
                                    Tap to view order details
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">📭</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                    <p class="text-gray-500 mb-6">
                        @if($filterStatus || $search)
                            Try adjusting your filters to see more results.
                        @else
                            You haven't placed any orders yet.
                        @endif
                    </p>
                    <a href="{{ route('menu.browse') }}"
                       class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors font-medium">
                        Browse Menu
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Scoped styles for order cards */
        .order-card {
            transition: all 0.2s ease-in-out;
        }

        .order-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</div>
