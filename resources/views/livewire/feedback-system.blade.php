<div>
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Feedback & Reviews</h1>
                    <p class="text-gray-600 mt-1">Share your dining experience and help us improve</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-500">{{ number_format($averageRating, 1) }}/5</div>
                            <div class="text-sm text-gray-500">Average Rating</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalFeedback }}</div>
                            <div class="text-sm text-gray-500">Reviews Given</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Feedback Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Submit Feedback</h2>

                    <!-- Order Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Order to Review
                        </label>
                        @if($completedOrders->count() > 0)
                            <div class="grid grid-cols-1 gap-3 max-h-60 overflow-y-auto">
                                @foreach($completedOrders as $order)
                                    <div class="border rounded-lg p-3 cursor-pointer hover:border-blue-500 transition-colors
                                              {{ $selectedOrder && $selectedOrder->id === $order->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}"
                                         wire:click="loadOrder({{ $order->id }})">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $order->meal->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $order->meal->canteen->name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    Ordered: {{ $order->created_at->format('M j, Y') }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-green-600">RM{{ number_format($order->total_amount, 2) }}</p>
                                                <p class="text-sm text-gray-500">Qty: {{ $order->quantity }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-4xl mb-4">📝</div>
                                <p>No completed orders available for feedback.</p>
                                <p class="text-sm mt-2">Complete an order first to leave feedback.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Feedback Form -->
                    @if($selectedOrder)
                        <form wire:submit="submitFeedback">
                            <div class="space-y-6">
                                <!-- Selected Order Info -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-semibold text-blue-900">{{ $selectedOrder->meal->name }}</h4>
                                            <p class="text-sm text-blue-700">{{ $selectedOrder->meal->canteen->name }}</p>
                                        </div>
                                        <button type="button"
                                                wire:click="$set('selectedOrder', null)"
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            Change Order
                                        </button>
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Overall Rating
                                    </label>
                                    <div class="flex space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button"
                                                    wire:click="$set('rating', {{ $i }})"
                                                    class="text-4xl focus:outline-none transition-transform hover:scale-110
                                                           {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}">
                                                ★
                                            </button>
                                        @endfor
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        @switch($rating)
                                            @case(1) Very Poor @break
                                            @case(2) Poor @break
                                            @case(3) Average @break
                                            @case(4) Good @break
                                            @case(5) Excellent @break
                                        @endswitch
                                    </div>
                                    @error('rating')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Categories -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        What did you like? (Select all that apply)
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach([
                                            'food_quality' => 'Food Quality',
                                            'taste' => 'Taste',
                                            'portion_size' => 'Portion Size',
                                            'service' => 'Service',
                                            'waiting_time' => 'Waiting Time',
                                            'value' => 'Value for Money',
                                            'cleanliness' => 'Cleanliness',
                                            'presentation' => 'Presentation'
                                        ] as $value => $label)
                                            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50
                                                      {{ in_array($value, $categories) ? 'bg-blue-50 border-blue-200' : '' }}">
                                                <input type="checkbox"
                                                       wire:model="categories"
                                                       value="{{ $value }}"
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-3 text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Comment -->
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Your Comments
                                    </label>
                                    <textarea wire:model="comment"
                                              id="comment"
                                              rows="4"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Share your experience with this meal... What did you like? Any suggestions for improvement?"></textarea>
                                    @error('comment')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ strlen($comment) }}/1000 characters
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button type="submit"
                                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-semibold text-lg">
                                        Submit Feedback
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-4">👆</div>
                            <p>Select an order above to leave feedback</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Previous Feedback -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Your Previous Feedback</h2>

                    @if($userFeedback->count() > 0)
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @foreach($userFeedback as $feedback)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $feedback->meal->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $feedback->created_at->format('M j, Y') }}</p>
                                        </div>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="text-sm {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>

                                    @if($feedback->comment)
                                        <p class="text-gray-700 text-sm mt-2">{{ $feedback->comment }}</p>
                                    @endif

                                    @if($feedback->categories)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($feedback->categories as $category)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 capitalize">
                                                    {{ str_replace('_', ' ', $category) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $userFeedback->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-4">💬</div>
                            <p>No feedback submitted yet</p>
                            <p class="text-sm mt-2">Your feedback will appear here</p>
                        </div>
                    @endif
                </div>

                <!-- Feedback Stats -->
                <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Feedback Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Reviews</span>
                            <span class="font-semibold">{{ $totalFeedback }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Average Rating</span>
                            <div class="flex items-center">
                                <span class="font-semibold mr-2">{{ number_format($averageRating, 1) }}</span>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-sm {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending Reviews</span>
                            <span class="font-semibold">{{ $completedOrders->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
