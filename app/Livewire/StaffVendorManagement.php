<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Canteen;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class StaffVendorManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $showModal = false;
    public $editingUser = null;
    public $availableCanteens = [];

    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'vendor';
    public $canteen_id = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'nullable|string|min:8',
        'role' => 'required|in:vendor,staff,student',
        'canteen_id' => 'nullable|exists:canteens,id',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->availableCanteens = Canteen::where('is_active', true)->get();
    }

    public function render()
    {
        $users = User::with(['canteen']) // Changed from 'canteen' to 'canteen' (keeping same but ensuring it matches)
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $userStats = [
            'total' => User::count(),
            'vendors' => User::where('role', 'vendor')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'students' => User::where('role', 'student')->count(),
            'active' => User::where('is_active', true)->count(),
        ];

        return view('livewire.staff-vendor-management', compact('users', 'userStats'));
    }

    public function createUser()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editingUser = null;
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);

        $this->editingUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->canteen_id = $user->canteen_id;
        $this->is_active = $user->is_active;
        $this->password = ''; // Don't fill password

        $this->showModal = true;
    }
    public function clearFilters()
    {
        $this->reset(['search', 'roleFilter']);
    }
    public function saveUser()
    {
        if ($this->editingUser) {
            $this->rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->editingUser->id;
            $this->rules['password'] = 'nullable|string|min:8';
        }

        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'canteen_id' => $this->role === 'vendor' ? $this->canteen_id : null,
            'is_active' => $this->is_active,
        ];

        // Only update password if provided
        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        DB::transaction(function () use ($userData) {
            if ($this->editingUser) {
                // Handle canteen updates for existing user
                $oldCanteenId = $this->editingUser->canteen_id;
                $newCanteenId = $this->role === 'vendor' ? $this->canteen_id : null;

                // If canteen changed or role changed from vendor
                if ($oldCanteenId !== $newCanteenId) {
                    // Clear old canteen assignment
                    if ($oldCanteenId) {
                        $oldCanteen = Canteen::find($oldCanteenId);
                        if ($oldCanteen) {
                            $oldCanteen->update(['vendor_id' => null]);
                        }
                    }

                    // Set new canteen assignment
                    if ($newCanteenId && $this->role === 'vendor') {
                        $newCanteen = Canteen::find($newCanteenId);
                        if ($newCanteen) {
                            // Check if canteen already has a vendor
                            if ($newCanteen->vendor_id && $newCanteen->vendor_id !== $this->editingUser->id) {
                                $existingVendor = User::find($newCanteen->vendor_id);
                                // Clear the canteen assignment from the previous vendor
                                if ($existingVendor) {
                                    $existingVendor->update(['canteen_id' => null]);
                                }
                            }
                            $newCanteen->update(['vendor_id' => $this->editingUser->id]);
                        }
                    }
                }

                $this->editingUser->update($userData);
            } else {
                // Create new user
                $user = User::create(array_merge($userData, [
                    'password' => Hash::make($this->password),
                ]));

                // Handle canteen assignment for new vendor
                if ($this->role === 'vendor' && $this->canteen_id) {
                    $canteen = Canteen::find($this->canteen_id);
                    if ($canteen) {
                        // Check if canteen already has a vendor
                        if ($canteen->vendor_id) {
                            $existingVendor = User::find($canteen->vendor_id);
                            // Clear the canteen assignment from the previous vendor
                            if ($existingVendor) {
                                $existingVendor->update(['canteen_id' => null]);
                            }
                        }
                        $canteen->update(['vendor_id' => $user->id]);
                    }
                }
            }
        });

        $message = $this->editingUser ? 'User updated successfully.' : 'User created successfully.';
        session()->flash('success', $message);

        $this->resetForm();
        $this->showModal = false;
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "User {$status} successfully.");
    }

    public function assignCanteen($userId, $canteenId)
    {
        $user = User::findOrFail($userId);

        if ($user->role !== 'vendor') {
            session()->flash('error', 'Only vendors can be assigned to canteens.');
            return;
        }

        // Check if canteen is already assigned to another active vendor
        $existingVendor = User::where('canteen_id', $canteenId)
            ->where('id', '!=', $userId)
            ->where('is_active', true)
            ->first();

        if ($existingVendor) {
            session()->flash('error', 'This canteen is already assigned to another active vendor: ' . $existingVendor->name);
            return;
        }

        // Get the canteen
        $canteen = Canteen::findOrFail($canteenId);

        // Start a database transaction to ensure consistency
        DB::transaction(function () use ($user, $canteen) {
            // Clear previous canteen assignment if any
            if ($user->canteen_id) {
                $previousCanteen = Canteen::find($user->canteen_id);
                if ($previousCanteen) {
                    $previousCanteen->update(['vendor_id' => null]);
                }
            }

            // Update user with new canteen
            $user->update(['canteen_id' => $canteen->id]);

            // Update canteen with new vendor
            $canteen->update(['vendor_id' => $user->id]);
        });

        session()->flash('success', 'Canteen assigned successfully.');
    }

    public function removeCanteen($userId)
    {
        $user = User::findOrFail($userId);

        // Get the current canteen before removal
        $currentCanteen = $user->canteen;

        DB::transaction(function () use ($user, $currentCanteen) {
            // Remove canteen assignment from user
            $user->update(['canteen_id' => null]);

            // Remove vendor assignment from canteen
            if ($currentCanteen) {
                $currentCanteen->update(['vendor_id' => null]);
            }
        });

        session()->flash('success', 'Canteen assignment removed.');
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent deletion of own account
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'email', 'password', 'role',
            'canteen_id', 'is_active'
        ]);
        $this->editingUser = null;
    }

    public function getRoleBadgeColor($role)
    {
        return match($role) {
            'vendor' => 'bg-orange-100 text-orange-800',
            'staff' => 'bg-blue-100 text-blue-800',
            'student' => 'bg-green-100 text-green-800',
            'admin' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getRoleIcon($role)
    {
        return match($role) {
            'vendor' => '🏪',
            'staff' => '👨‍💼',
            'student' => '🎓',
            'admin' => '⚙️',
            default => '👤'
        };
    }

    public function updatedRole($value)
    {
        // Reset canteen_id if role is not vendor
        if ($value !== 'vendor') {
            $this->canteen_id = '';
        }
    }
}
