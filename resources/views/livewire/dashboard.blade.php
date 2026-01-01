<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        @auth
                            @if($userRole === 'student')
                                Welcome back, {{ Auth::user()->name }}! 🎓
                            @elseif($userRole === 'vendor')
                                Vendor Dashboard, {{ Auth::user()->name }} 🏪
                            @elseif($userRole === 'staff')
                                Staff Dashboard, {{ Auth::user()->name }} 👨‍💼
                            @elseif($userRole === 'admin')
                                Admin Dashboard, {{ Auth::user()->name }} ⚙️
                            @else
                                Welcome, {{ Auth::user()->name }}!
                            @endif
                        @else
                            Welcome to Campus Eats 🍽️
                        @endauth
                    </h1>
                    <p class="text-gray-600 mt-1">
                        @if($userRole === 'student')
                            Your campus dining dashboard - Order delicious meals from your favorite canteens!
                        @elseif($userRole === 'vendor')
                            Manage your canteen operations, orders, and menu items efficiently.
                        @elseif($userRole === 'staff')
                            Monitor and manage all canteen operations across campus.
                        @elseif($userRole === 'admin')
                            System administration panel - Manage users, canteens, and platform settings.
                        @else
                            Discover delicious meals from campus canteens. Order now or register to get started!
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-lg font-semibold text-gray-900">{{ now()->format('h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($this->getStatCards() as $stat)
                <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-300">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl">{{ $stat['icon'] }}</div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        {{ $stat['title'] }}
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $stat['value'] }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Dashboard Images Section -->
        @if(count($dashboardImages) > 0)
            <div class="mb-8 bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Featured Highlights</h3>
                    <p class="text-sm text-gray-500 mt-1">Discover the best of campus dining</p>
                </div>

                <!-- Horizontal Scrollable Image Gallery -->
                <div class="relative">
                    <div class="overflow-x-auto scrollbar-hide">
                        <div class="flex space-x-4 p-6 min-w-max">
                            @foreach($dashboardImages as $image)
                                <div class="flex-shrink-0 w-80 group">
                                    <div class="relative overflow-hidden rounded-lg shadow-md">
                                        <img
                                            src="{{ $image->image_url }}"
                                            alt="{{ $image->title }}"
                                            class="w-full h-48 object-cover transform group-hover:scale-105 transition-transform duration-300"
                                        >
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                            <h4 class="font-semibold text-lg">{{ $image->title }}</h4>
                                            @if($image->description)
                                                <p class="text-sm opacity-90 mt-1">{{ $image->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Scroll Indicators -->
                    <div class="flex justify-center space-x-2 py-4">
                        @foreach($dashboardImages as $index => $image)
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <!-- Replace Quick Actions with Recent Orders Section -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            @if($userRole === 'student')
                                My Recent Orders
                            @elseif($userRole === 'vendor')
                                Recent Orders
                            @else
                                Recent Activity
                            @endif
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(count($recentOrders) > 0)
                            <div class="space-y-4">
                                @foreach($recentOrders as $order)
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-800">#{{ $order->queue_number ?? $order->id }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $order->meal->name ?? 'Unknown Meal' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $order->created_at->diffForHumans() }}
                                                </p>
                                                @if($userRole !== 'student')
                                                    <p class="text-xs text-gray-400">
                                                        {{ $order->user->name }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                               ($order->status === 'preparing' ? 'bg-yellow-100 text-yellow-800' :
                                               ($order->status === 'ready' ? 'bg-blue-100 text-blue-800' :
                                               ($order->status === 'confirmed' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-4">
                                    @if($userRole === 'student')
                                        📭
                                    @elseif($userRole === 'vendor')
                                        🛒
                                    @else
                                        📊
                                    @endif
                                </div>
                                <p class="text-gray-500">
                                    @if($userRole === 'student')
                                        No recent orders
                                    @elseif($userRole === 'vendor')
                                        No orders yet
                                    @else
                                        No recent activity
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Sections based on role -->
                @if($userRole === 'vendor' && count($popularMeals) > 0)
                    <div class="bg-white shadow rounded-lg mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Popular Meals</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($popularMeals as $meal)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @if($meal->image_url)
                                                <img src="{{ $meal->image_url }}"
                                                     alt="{{ $meal->name }}"
                                                     class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-gray-400 text-lg">🍽️</span>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $meal->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $meal->total_orders ?? 0 }} orders</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-semibold text-green-600">
                                                RM{{ number_format($meal->price, 2) }}
                                            </span>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $meal->is_available ? 'Available' : 'Unavailable' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- System Status (Admin/Staff) -->
                @if(in_array($userRole, ['admin', 'staff']))
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">System Status</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Platform Status</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Operational
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Active Canteens</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['total_canteens'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Active Users</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['total_users'] ?? \App\Models\User::count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Canteen Info (Vendor) -->
                @if($userRole === 'vendor' && isset($stats['canteen_name']))
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Canteen Info</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Canteen Name</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['canteen_name'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Today's Revenue</span>
                                    <span class="text-sm font-medium text-green-600">RM{{ number_format($stats['today_revenue'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pending Orders</span>
                                    <span class="text-sm font-medium text-orange-600">{{ $stats['pending_orders'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Styles moved inside the root div -->
    <style>
        /* Hide scrollbar but allow scrolling */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;  /* Chrome, Safari and Opera */
        }
    </style>

    <!-- Script moved inside the root div -->
    @script
    <script>
        // Auto-refresh dashboard every 60 seconds
        setInterval(() => {
            $wire.loadDashboardData();
        }, 60000);
    </script>
    @endscript
</div>
