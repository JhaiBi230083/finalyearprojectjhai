<div>
    <!-- SINGLE ROOT ELEMENT - Everything must be inside this div -->

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search Meals</label>
                <input type="text" wire:model.live.debounce.500ms="search" id="search"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Search meals...">
            </div>

            <!-- Category Dropdown -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model.live="category" id="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Canteen Dropdown -->
            <div>
                <label for="canteenId" class="block text-sm font-medium text-gray-700">Canteen</label>
                <select wire:model.live="canteenId" id="canteenId"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Canteens</option>
                    @foreach($canteens as $canteen)
                        <option value="{{ $canteen->id }}">{{ $canteen->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By Dropdown -->
            <div>
                <label for="sortBy" class="block text-sm font-medium text-gray-700">Sort By</label>
                <select wire:model.live="sortBy" id="sortBy"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="name">Name</option>
                    <option value="price">Price</option>
                    <option value="created_at">Newest</option>
                </select>
            </div>
        </div>

        <!-- Active Filters & Clear Button -->
        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <!-- Active Filters -->
            <div class="flex flex-wrap gap-2">
                @if($search)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Search: "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-1 hover:text-blue-600">
                            ×
                        </button>
                    </span>
                @endif
                @if($category)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Category: {{ $category }}
                        <button wire:click="$set('category', '')" class="ml-1 hover:text-green-600">
                            ×
                        </button>
                    </span>
                @endif
                @if($canteenId)
                    @php
                        $selectedCanteen = $canteens->firstWhere('id', $canteenId);
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Canteen: {{ $selectedCanteen->name ?? 'Unknown' }}
                        <button wire:click="$set('canteenId', '')" class="ml-1 hover:text-purple-600">
                            ×
                        </button>
                    </span>
                @endif
            </div>

            <!-- Clear Filters Button -->
            <button wire:click="clearFilters"
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors text-sm font-medium">
                Clear All Filters
            </button>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-4 flex justify-between items-center">
        <p class="text-gray-600">
            Showing {{ $meals->firstItem() ?? 0 }}-{{ $meals->lastItem() ?? 0 }} of {{ $meals->total() }} results
        </p>

        <!-- Sort Direction Toggle -->
        <button wire:click="toggleSortDirection"
                class="flex items-center gap-1 text-sm text-gray-600 hover:text-gray-800">
            <span>Sort: {{ ucfirst($sortBy) }}</span>
            @if($sortDirection === 'asc')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            @endif
        </button>
    </div>

    <!-- Meals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 meal-card-container">
        @foreach($meals as $meal)
            <div class="bg-white rounded-lg shadow-md overflow-hidden meal-card hover:shadow-lg transition-all duration-300">
                <!-- Meal Image - FIXED VERSION -->
                @if($meal->image_url)
                    <img src="{{ asset($meal->image_url) }}" alt="{{ $meal->name }}"
                         class="w-full h-48 object-cover"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center hidden">
                        <div class="text-center">
                            <div class="text-4xl mb-2 text-gray-400">🍽️</div>
                            <span class="text-gray-500 text-sm">Image not available</span>
                        </div>
                    </div>
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-4xl mb-2 text-gray-400">🍽️</div>
                            <span class="text-gray-500 text-sm">No Image</span>
                        </div>
                    </div>
                @endif

                <!-- Meal Content -->
                <div class="p-4">
                    <!-- Header with Name and Price -->
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">{{ $meal->name }}</h3>
                        <span class="text-lg font-bold text-green-600 whitespace-nowrap">RM{{ number_format($meal->price, 2) }}</span>
                    </div>

                    <!-- Description -->
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $meal->description }}</p>

                    <!-- Badges -->
                    <div class="flex justify-between items-center mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 max-w-[120px] truncate">
                            {{ $meal->canteen->name }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $meal->category }}
                        </span>
                    </div>

                    <!-- Footer Info -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $meal->preparation_time }} min
                        </span>
                        <div class="flex items-center">
                            <span class="text-yellow-400">★</span>
                            <span class="text-sm text-gray-600 ml-1">
                                {{ number_format($meal->averageRating(), 1) }}
                            </span>
                        </div>
                    </div>

                    <!-- Order Button -->
                    <div class="mt-2">
                        <a href="{{ route('order.place', ['mealId' => $meal->id]) }}"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center block font-medium">
                            Order Now
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($meals->hasPages())
        <div class="mt-8">
            {{ $meals->links() }}
        </div>
    @endif

    <!-- Empty State -->
    @if($meals->isEmpty())
        <div class="text-center py-16">
            <div class="text-gray-400 text-8xl mb-6">🍽️</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-3">No meals found</h3>
            <p class="text-gray-500 text-lg mb-6 max-w-md mx-auto">
                @if($search || $category || $canteenId)
                    Try adjusting your search filters or clear them to see all available meals.
                @else
                    No meals are currently available. Please check back later.
                @endif
            </p>
            @if($search || $category || $canteenId)
                <button wire:click="clearFilters"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Clear All Filters
                </button>
            @endif
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Loading meals...</span>
        </div>
    </div>

    <!-- Styles (inside the root div) -->
    <style>
        /* Custom CSS for the 3D perspective effect */
        .meal-card-container {
            perspective: 1000px;
        }

        .meal-card {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            transform-style: preserve-3d;
        }

        .meal-card:hover {
            transform: translateZ(20px) rotateY(2deg);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Line clamp utilities */
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        /* Smooth transitions for all interactive elements */
        .meal-card * {
            transition: all 0.3s ease;
        }
    </style>

    <!-- Scripts (inside the root div) -->
    @script
    <script>
        // Add some JavaScript for enhanced interactivity
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            // Add any custom JavaScript behavior here if needed
        });
    </script>
    @endscript
</div>
