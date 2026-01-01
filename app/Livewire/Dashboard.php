<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Canteen;
use App\Models\Meal;
use App\Models\User;
use App\Models\DashboardImage; // Add this import
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $stats = [];
    public $recentOrders = [];
    public $popularMeals = [];
    public $quickActions = [];
    public $userRole;
    public $dashboardImages = []; // Add this property

    public function mount()
    {
        $this->userRole = Auth::user()->role ?? 'guest';
        $this->loadDashboardData();
        $this->loadDashboardImages(); // Call this method
    }

    public function loadDashboardImages()
    {
        // Check if DashboardImage model exists
        if (class_exists(DashboardImage::class)) {
            $this->dashboardImages = DashboardImage::active()
                ->ordered()
                ->get();
        } else {
            $this->dashboardImages = collect(); // Return empty collection if model doesn't exist
        }
    }

    public function loadDashboardData()
    {
        $user = Auth::user();

        if ($this->userRole === 'student') {
            $this->loadStudentDashboard($user);
        } elseif ($this->userRole === 'vendor') {
            $this->loadVendorDashboard($user);
        } elseif ($this->userRole === 'staff') {
            $this->loadStaffDashboard($user);
        } elseif ($this->userRole === 'admin') {
            $this->loadAdminDashboard($user);
        } else {
            $this->loadGuestDashboard();
        }
    }

    private function loadStudentDashboard($user)
    {
        // Student Statistics
        $todayOrders = Order::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        $totalOrders = Order::where('user_id', $user->id)->count();

        $pendingOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->count();

        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $this->stats = [
            'today_orders' => $todayOrders,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_spent' => $totalSpent,
        ];

        // Recent Orders
        $this->recentOrders = Order::where('user_id', $user->id)
            ->with(['meal.canteen'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Quick Actions for Students (View Queue removed)
        $this->quickActions = [
            [
                'title' => 'Browse Menu',
                'description' => 'Order food from canteens',
                'icon' => '🍽️',
                'url' => route('menu.browse'),
                'color' => 'bg-blue-500'
            ],
            [
                'title' => 'Order History',
                'description' => 'View your past orders',
                'icon' => '📝',
                'url' => route('order.history'),
                'color' => 'bg-purple-500'
            ],
            [
                'title' => 'Give Feedback',
                'description' => 'Share your experience',
                'icon' => '⭐',
                'url' => route('feedback'),
                'color' => 'bg-yellow-500'
            ],
            [
                'title' => 'Profile Settings',
                'description' => 'Update your account details',
                'icon' => '👤',
                'url' => route('profile.edit'),
                'color' => 'bg-green-500'
            ]
        ];
    }

    private function loadVendorDashboard($user)
    {
        // Get vendor's canteen
        $canteen = Canteen::where('vendor_id', $user->id)->first();

        if ($canteen) {
            $mealIds = Meal::where('canteen_id', $canteen->id)->pluck('id');

            $todayOrders = Order::whereIn('meal_id', $mealIds)
                ->whereDate('created_at', today())
                ->count();

            $pendingOrders = Order::whereIn('meal_id', $mealIds)
                ->whereIn('status', ['pending', 'confirmed', 'preparing'])
                ->count();

            $todayRevenue = Order::whereIn('meal_id', $mealIds)
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_amount');

            $totalRevenue = Order::whereIn('meal_id', $mealIds)
                ->where('status', 'completed')
                ->sum('total_amount');

            $this->stats = [
                'today_orders' => $todayOrders,
                'pending_orders' => $pendingOrders,
                'today_revenue' => $todayRevenue,
                'total_revenue' => $totalRevenue,
                'canteen_name' => $canteen->name,
            ];

            // Recent Orders for Vendor
            $this->recentOrders = Order::whereIn('meal_id', $mealIds)
                ->with(['user', 'meal'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Popular Meals
            $this->popularMeals = Meal::where('canteen_id', $canteen->id)
                ->withCount(['orders as total_orders' => function($query) {
                    $query->where('status', 'completed');
                }])
                ->orderBy('total_orders', 'desc')
                ->limit(5)
                ->get();

        } else {
            $this->stats = [
                'today_orders' => 0,
                'pending_orders' => 0,
                'today_revenue' => 0,
                'total_revenue' => 0,
                'canteen_name' => 'No Canteen Assigned',
            ];

            $this->recentOrders = collect();
            $this->popularMeals = collect();
        }

        // Quick Actions for Vendors (View Queue removed)
        $this->quickActions = [
            [
                'title' => 'Manage Menu',
                'description' => 'Add or update menu items',
                'icon' => '📋',
                'url' => route('vendor.menu'),
                'color' => 'bg-blue-500'
            ],
            [
                'title' => 'Manage Orders',
                'description' => 'Process incoming orders',
                'icon' => '🛒',
                'url' => route('vendor.orders'),
                'color' => 'bg-purple-500'
            ],
            [
                'title' => 'View Analytics',
                'description' => 'Sales and performance reports',
                'icon' => '📈',
                'url' => '#',
                'color' => 'bg-green-500'
            ],
            [
                'title' => 'Canteen Settings',
                'description' => 'Update canteen information',
                'icon' => '⚙️',
                'url' => '#',
                'color' => 'bg-yellow-500'
            ]
        ];
    }

    private function loadStaffDashboard($user)
    {
        // Staff can see all canteens
        $totalCanteens = Canteen::where('is_active', true)->count();
        $totalMeals = Meal::where('is_available', true)->count();

        $todayOrders = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed', 'preparing'])->count();

        $this->stats = [
            'total_canteens' => $totalCanteens,
            'total_meals' => $totalMeals,
            'today_orders' => $todayOrders,
            'pending_orders' => $pendingOrders,
        ];

        // Recent Orders
        $this->recentOrders = Order::with(['user', 'meal.canteen'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Quick Actions for Staff (View Queue removed)
        $this->quickActions = [
            [
                'title' => 'Manage All Menus',
                'description' => 'Manage all canteen menus',
                'icon' => '📋',
                'url' => route('staff.menu'),
                'color' => 'bg-blue-500'
            ],
            [
                'title' => 'Order Management',
                'description' => 'Manage all orders',
                'icon' => '🛒',
                'url' => '#',
                'color' => 'bg-purple-500'
            ],
            [
                'title' => 'System Overview',
                'description' => 'View system statistics',
                'icon' => '🏢',
                'url' => '#',
                'color' => 'bg-yellow-500'
            ],
            [
                'title' => 'User Management',
                'description' => 'Manage user accounts',
                'icon' => '👥',
                'url' => '#',
                'color' => 'bg-green-500'
            ]
        ];
    }

    private function loadAdminDashboard($user)
    {
        // Admin statistics
        $totalUsers = User::count();
        $totalCanteens = Canteen::count();
        $totalOrders = Order::count();
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $this->stats = [
            'total_users' => $totalUsers,
            'total_canteens' => $totalCanteens,
            'total_orders' => $totalOrders,
            'today_revenue' => $todayRevenue,
        ];

        // Recent Orders
        $this->recentOrders = Order::with(['user', 'meal.canteen'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Quick Actions for Admin (View Queue was not present here originally)
        $this->quickActions = [
            [
                'title' => 'User Management',
                'description' => 'Manage system users',
                'icon' => '👥',
                'url' => '#',
                'color' => 'bg-blue-500'
            ],
            [
                'title' => 'Canteen Management',
                'description' => 'Manage all canteens',
                'icon' => '🏪',
                'url' => '#',
                'color' => 'bg-green-500'
            ],
            [
                'title' => 'System Analytics',
                'description' => 'View system reports',
                'icon' => '📈',
                'url' => '#',
                'color' => 'bg-purple-500'
            ],
            [
                'title' => 'Settings',
                'description' => 'System configuration',
                'icon' => '⚙️',
                'url' => '#',
                'color' => 'bg-yellow-500'
            ]
        ];
    }

    private function loadGuestDashboard()
    {
        $this->stats = [
            'total_canteens' => Canteen::where('is_active', true)->count(),
            'total_meals' => Meal::where('is_available', true)->count(),
            'active_orders' => Order::whereIn('status', ['pending', 'confirmed', 'preparing'])->count(),
            'join_now' => 'Register Today',
        ];

        // Quick Actions for Guests (View Queue removed)
        $this->quickActions = [
            [
                'title' => 'Browse Menu',
                'description' => 'View available meals',
                'icon' => '🍽️',
                'url' => route('menu.browse'),
                'color' => 'bg-blue-500'
            ],
            [
                'title' => 'Login',
                'description' => 'Access your account',
                'icon' => '🔐',
                'url' => route('login'),
                'color' => 'bg-purple-500'
            ],
            [
                'title' => 'Register',
                'description' => 'Create new account',
                'icon' => '📝',
                'url' => route('register'),
                'color' => 'bg-yellow-500'
            ],
            [
                'title' => 'View Canteens',
                'description' => 'Explore available canteens',
                'icon' => '🏪',
                'url' => '#',
                'color' => 'bg-green-500'
            ]
        ];
    }

    public function getStatCards()
    {
        if ($this->userRole === 'student') {
            return [
                [
                    'title' => "Today's Orders",
                    'value' => $this->stats['today_orders'],
                    'icon' => '📦'
                ],
                [
                    'title' => 'Total Orders',
                    'value' => $this->stats['total_orders'],
                    'icon' => '📝'
                ],
                [
                    'title' => 'Pending Orders',
                    'value' => $this->stats['pending_orders'],
                    'icon' => '⏳'
                ],
                [
                    'title' => 'Total Spent',
                    'value' => 'RM' . number_format($this->stats['total_spent'], 2),
                    'icon' => '💰'
                ]
            ];
        } elseif ($this->userRole === 'vendor') {
            return [
                [
                    'title' => "Today's Orders",
                    'value' => $this->stats['today_orders'],
                    'icon' => '📦'
                ],
                [
                    'title' => 'Pending Orders',
                    'value' => $this->stats['pending_orders'],
                    'icon' => '⏳'
                ],
                [
                    'title' => "Today's Revenue",
                    'value' => 'RM' . number_format($this->stats['today_revenue'], 2),
                    'icon' => '💰'
                ],
                [
                    'title' => 'Total Revenue',
                    'value' => 'RM' . number_format($this->stats['total_revenue'], 2),
                    'icon' => '🏦'
                ]
            ];
        } elseif ($this->userRole === 'staff') {
            return [
                [
                    'title' => 'Active Canteens',
                    'value' => $this->stats['total_canteens'],
                    'icon' => '🏪'
                ],
                [
                    'title' => 'Available Meals',
                    'value' => $this->stats['total_meals'],
                    'icon' => '🍽️'
                ],
                [
                    'title' => "Today's Orders",
                    'value' => $this->stats['today_orders'],
                    'icon' => '📦'
                ],
                [
                    'title' => 'Pending Orders',
                    'value' => $this->stats['pending_orders'],
                    'icon' => '⏳'
                ]
            ];
        } elseif ($this->userRole === 'admin') {
            return [
                [
                    'title' => 'Total Users',
                    'value' => $this->stats['total_users'],
                    'icon' => '👥'
                ],
                [
                    'title' => 'Total Canteens',
                    'value' => $this->stats['total_canteens'],
                    'icon' => '🏪'
                ],
                [
                    'title' => 'Total Orders',
                    'value' => $this->stats['total_orders'],
                    'icon' => '📦'
                ],
                [
                    'title' => "Today's Revenue",
                    'value' => 'RM' . number_format($this->stats['today_revenue'], 2),
                    'icon' => '💰'
                ]
            ];
        } else {
            return [
                [
                    'title' => 'Active Canteens',
                    'value' => $this->stats['total_canteens'],
                    'icon' => '🏪'
                ],
                [
                    'title' => 'Available Meals',
                    'value' => $this->stats['total_meals'],
                    'icon' => '🍽️'
                ],
                [
                    'title' => 'Active Orders',
                    'value' => $this->stats['active_orders'],
                    'icon' => '📦'
                ],
                [
                    'title' => 'Join Now',
                    'value' => $this->stats['join_now'],
                    'icon' => '🎯'
                ]
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
