<div>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manage Orders</h1>
                <p class="text-gray-600 mt-2">Manage and track orders for {{ $canteen->name ?? 'your canteen' }}</p>
            </div>
            <div class="flex space-x-3">

            </div>
        </div>

        @if(!$canteen)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                No canteen assigned to your account. Please contact administrator.
            </div>
        @endif

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ session('info') }}
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-utensils text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Preparing</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['preparing'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ready</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['ready'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['today'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
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
                               placeholder="Search by order #, customer, or meal..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 w-full md:w-64">
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">{{ $orders->total() }} orders found</span>
                    <button wire:click="$set('filterStatus', '')"
                            wire:click="$set('search', '')"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <span class="text-blue-600 font-bold">#{{ $order->queue_number ?? $order->id }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->order_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $order->meal->name }} × {{ $order->quantity }}
                                        </div>
                                        <div class="text-sm font-semibold text-green-600">
                                            RM{{ number_format($order->total_amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                @if($order->special_instructions)
                                    <div class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-sticky-note mr-1"></i>
                                        {{ Str::limit($order->special_instructions, 30) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $this->getStatusColor($order->status) }}">
                                        {{ $this->getStatusIcon($order->status) }} {{ ucfirst($order->status) }}
                                    </span>
                                @if($order->status === 'preparing' && $order->preparation_started_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Prep: {{ $this->calculatePreparationTime($order) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $order->created_at->format('M j, Y') }}</div>
                                <div>{{ $order->created_at->format('g:i A') }}</div>
                                <div class="text-xs text-gray-400">
                                    Pickup: {{ $order->pickup_time->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-col space-y-2">
                                    <!-- Status Action Buttons -->
                                    @foreach($this->getActionButtons($order->status) as $action)
                                        <button wire:click="updateOrderStatus({{ $order->id }}, '{{ $action['status'] }}')"
                                                class="text-{{ $action['color'] }}-600 hover:text-{{ $action['color'] }}-800 text-xs font-medium py-1 px-2 border border-{{ $action['color'] }}-300 rounded hover:bg-{{ $action['color'] }}-50 transition-colors">
                                            {{ $action['label'] }}
                                        </button>
                                    @endforeach

                                    <!-- View Details Button -->
                                    <button wire:click="viewOrderDetails({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium py-1 px-2 border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="text-gray-400 text-6xl mb-4">📭</div>
                                <p class="text-gray-500 text-lg">No orders found</p>
                                <p class="text-gray-400 text-sm mt-2">
                                    @if($filterStatus || $search)
                                        Try adjusting your filters
                                    @else
                                        Orders will appear here when customers place them
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Order Details Modal -->
    @if($showOrderModal && $selectedOrder)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Order #{{ $selectedOrder->order_number }}</h2>
                            <p class="text-gray-600">Placed on {{ $selectedOrder->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <button wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Order Status -->
                    <div class="mb-6 p-4 rounded-lg border {{ $this->getStatusColor($selectedOrder->status) }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">{{ $this->getStatusIcon($selectedOrder->status) }}</span>
                                <div>
                                    <h3 class="font-semibold">Current Status: {{ ucfirst($selectedOrder->status) }}</h3>
                                    <p class="text-sm opacity-75">
                                        @if($selectedOrder->status === 'preparing' && $selectedOrder->preparation_started_at)
                                            Preparation started: {{ $selectedOrder->preparation_started_at->diffForHumans() }}
                                        @elseif($selectedOrder->status === 'ready' && $selectedOrder->ready_at)
                                            Ready since: {{ $selectedOrder->ready_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">RM{{ number_format($selectedOrder->total_amount, 2) }}</div>
                                <div class="text-sm text-gray-600">Queue #{{ $selectedOrder->queue_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Customer Information</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium">{{ $selectedOrder->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $selectedOrder->user->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order Number:</span>
                                    <span class="font-mono">{{ $selectedOrder->order_number }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">Order Timeline</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order Placed:</span>
                                    <span>{{ $selectedOrder->created_at->format('g:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pickup Time:</span>
                                    <span>{{ $selectedOrder->pickup_time->format('g:i A') }}</span>
                                </div>
                                @if($selectedOrder->preparation_started_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Prep Started:</span>
                                        <span>{{ $selectedOrder->preparation_started_at->format('g:i A') }}</span>
                                    </div>
                                @endif
                                @if($selectedOrder->ready_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ready At:</span>
                                        <span>{{ $selectedOrder->ready_at->format('g:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Order Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold">{{ $selectedOrder->meal->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $selectedOrder->meal->description }}</p>
                                    <p class="text-sm text-gray-500">Quantity: {{ $selectedOrder->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600">RM{{ number_format($selectedOrder->meal->price * $selectedOrder->quantity, 2) }}</div>
                                    <div class="text-sm text-gray-600">RM{{ number_format($selectedOrder->meal->price, 2) }} each</div>
                                </div>
                            </div>

                            @if($selectedOrder->special_instructions)
                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                    <div class="flex items-start">
                                        <i class="fas fa-sticky-note text-yellow-500 mt-1 mr-2"></i>
                                        <div>
                                            <span class="font-medium text-yellow-800">Special Instructions:</span>
                                            <p class="text-yellow-700 text-sm mt-1">{{ $selectedOrder->special_instructions }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-3">Quick Actions</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($this->getActionButtons($selectedOrder->status) as $action)
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, '{{ $action['status'] }}')"
                                        class="bg-{{ $action['color'] }}-600 hover:bg-{{ $action['color'] }}-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    {{ $action['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div><div class="flex space-x-3">
            <div class="flex space-x-3">
                <!-- Quick Export -->
                <button wire:click="quickExport"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-download mr-2"></i>Quick Export
                </button>

                <!-- Advanced Export Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                        <i class="fas fa-cog mr-2"></i>Advanced Export
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                         style="display: none;">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Export Options</h3>

                            <div class="space-y-4">
                                <!-- Date Range -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <input type="date"
                                                   wire:model="exportStartDate"
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <input type="date"
                                                   wire:model="exportEndDate"
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Filter</label>
                                    <select wire:model="exportStatus"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">All Statuses</option>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="preparing">Preparing</option>
                                        <option value="ready">Ready</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <!-- Export Button -->
                                <button wire:click="exportOrders"
                                        @click="open = false"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-medium transition duration-200">
                                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Filter</label>
                                <select wire:model="exportStatus"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="preparing">Preparing</option>
                                    <option value="ready">Ready</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <!-- Export Button -->
                            <button wire:click="exportOrders"
                                    @click="open = false"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-medium transition duration-200">
                                <i class="fas fa-file-excel mr-2"></i>Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Auto-refresh for active orders -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-refresh every 30 seconds to get new orders
            setInterval(() => {
            @this.$refresh();
            }, 30000);
        });
    </script>
</div>
