<div>
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        @if($meal)
            <!-- Meal Info -->
            <div class="p-6 border-b">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- FIXED IMAGE DISPLAY -->
                    <div class="w-full md:w-64 h-48 overflow-hidden rounded-lg">
                        @if($meal->image_url)
                            <img src="{{ asset($meal->image_url) }}"
                                 alt="{{ $meal->name }}"
                                 class="w-full h-full object-cover rounded-lg"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center hidden">
                                <div class="text-center">
                                    <div class="text-4xl mb-2 text-gray-400">🍽️</div>
                                    <span class="text-gray-500 text-sm">Image not available</span>
                                </div>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-4xl mb-2 text-gray-400">🍽️</div>
                                    <span class="text-gray-500 text-sm">No Image</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $meal->name }}</h1>
                        <p class="text-gray-600 mb-4">{{ $meal->description }}</p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="text-sm text-gray-500">Price</span>
                                <p class="text-2xl font-bold text-green-600">RM{{ number_format($meal->price, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Preparation Time</span>
                                <p class="text-lg font-semibold">⏱ {{ $meal->preparation_time }} min</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Canteen</span>
                                <p class="font-medium">{{ $meal->canteen->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Category</span>
                                <p class="font-medium">{{ $meal->category }}</p>
                            </div>
                        </div>

                        <!-- Nutritional Information -->
                        @if(!empty($this->nutritionalInfo))
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h4 class="font-semibold mb-2">Nutritional Information</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                                    @foreach($this->nutritionalInfo as $key => $value)
                                        <span class="capitalize">{{ $key }}: {{ $value }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Form -->
            <div class="p-6">
                <form wire:submit="placeOrder">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center">
                                <button type="button" wire:click="decrement"
                                        class="bg-gray-200 rounded-l-md px-4 py-2 hover:bg-gray-300">
                                    -
                                </button>
                                <input type="number" wire:model="quantity"
                                       class="w-16 text-center border-y border-gray-300 py-2" readonly>
                                <button type="button" wire:click="increment"
                                        class="bg-gray-200 rounded-r-md px-4 py-2 hover:bg-gray-300">
                                    +
                                </button>
                            </div>
                            @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pickup Time -->
                        <div>
                            <label for="pickupTime" class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Pickup Time
                            </label>
                            <input type="datetime-local" wire:model="pickupTime" id="pickupTime"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}">
                            @error('pickupTime')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Special Instructions -->
                        <div class="md:col-span-2">
                            <label for="specialInstructions" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Instructions
                            </label>
                            <textarea wire:model="specialInstructions" id="specialInstructions" rows="3"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Any special requests or dietary requirements..."></textarea>
                            @error('specialInstructions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Order Summary</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-600">{{ $meal->name }} x {{ $quantity }}</p>
                                <p class="text-sm text-gray-500">Pickup: {{ \Carbon\Carbon::parse($pickupTime)->format('M j, g:i A') }}</p>
                            </div>
                            <p class="text-xl font-bold text-green-600">
                                RM{{ number_format($meal->price * $quantity, 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition-colors font-semibold">
                            Place Order - RM{{ number_format($meal->price * $quantity, 2) }}
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="p-6 text-center">
                <div class="text-gray-400 text-6xl mb-4">🍽️</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Meal not found</h3>
                <p class="text-gray-500 mb-4">The selected meal could not be loaded.</p>
                <a href="{{ route('menu.browse') }}"
                   class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
                    Browse Menu
                </a>
            </div>
        @endif
    </div>

    <!-- Payment Modal -->
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Complete Payment</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select wire:model="paymentMethod"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="e-wallet">E-Wallet</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="online_banking">Online Banking</option>
                    </select>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        Total Amount: <span class="font-bold">RM{{ number_format($meal->price * $quantity, 2) }}</span>
                    </p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="processPayment"
                            class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700">
                        Pay Now
                    </button>
                    <button wire:click="$set('showPaymentModal', false)"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-md w-full p-6 text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Successful!</h3>
                <p class="text-gray-600 mb-6">
                    Your order has been placed successfully and payment has been processed.
                </p>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="text-sm text-gray-600">Order ID: <span class="font-mono font-bold">#{{ $latestOrderId }}</span></p>
                    <p class="text-sm text-gray-600 mt-1">Total Paid: <span class="font-bold text-green-600">RM{{ number_format($meal->price * $quantity, 2) }}</span></p>
                </div>

                <div class="flex flex-col gap-3">
                    <button wire:click="goToOrderDetails"
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition-colors font-semibold">
                        View Order Details
                    </button>
                    <button wire:click="goToOrderHistory"
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition-colors font-semibold">
                        Go to Order History
                    </button>
                    <button wire:click="closeSuccessModal"
                            class="w-full bg-gray-300 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-400 transition-colors">
                        Continue Browsing
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
