<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Meal;
use App\Models\Canteen;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class VendorMenuManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $canteen;
    public $showModal = false;
    public $editingMeal = null;
    public $image;

    // Form fields
    public $name = '';
    public $description = '';
    public $price = '';
    public $category = '';
    public $is_available = true;
    public $preparation_time = 15;
    public $image_url = '';

    // Nutritional fields (replacing JSON)
    public $calories = '';
    public $protein = '';
    public $carbs = '';
    public $fat = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'category' => 'required|string|max:255',
        'is_available' => 'boolean',
        'preparation_time' => 'required|integer|min:1',
        'image' => 'nullable|image|max:2048',
        'calories' => 'nullable|integer|min:0',
        'protein' => 'nullable|string|max:20',
        'carbs' => 'nullable|string|max:20',
        'fat' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        // Get vendor's canteen
        $this->canteen = Canteen::where('vendor_id', Auth::id())->first();

        if (!$this->canteen) {
            session()->flash('error', 'No canteen assigned to your account.');
        }
    }

    public function render()
    {
        $meals = Meal::where('canteen_id', $this->canteen?->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Meal::where('canteen_id', $this->canteen?->id)
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('livewire.vendor-menu-management', compact('meals', 'categories'));
    }

    public function createMeal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editingMeal = null;
    }

    public function editMeal(Meal $meal)
    {
        if ($meal->canteen_id !== $this->canteen?->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->editingMeal = $meal;
        $this->name = $meal->name;
        $this->description = $meal->description;
        $this->price = $meal->price;
        $this->category = $meal->category;
        $this->is_available = $meal->is_available;
        $this->preparation_time = $meal->preparation_time;
        $this->image_url = $meal->image_url;

        // Parse nutritional info from JSON to individual fields
        $nutritionalInfo = json_decode($meal->nutritional_info ?? '{}', true);
        $this->calories = $nutritionalInfo['calories'] ?? '';
        $this->protein = $nutritionalInfo['protein'] ?? '';
        $this->carbs = $nutritionalInfo['carbs'] ?? '';
        $this->fat = $nutritionalInfo['fat'] ?? '';

        $this->showModal = true;
    }

    public function saveMeal()
    {
        $this->validate();

        if (!$this->canteen) {
            session()->flash('error', 'No canteen assigned.');
            return;
        }

        // Build nutritional info JSON from individual fields
        $nutritionalInfo = [];
        if (!empty($this->calories)) $nutritionalInfo['calories'] = $this->calories;
        if (!empty($this->protein)) $nutritionalInfo['protein'] = $this->protein;
        if (!empty($this->carbs)) $nutritionalInfo['carbs'] = $this->carbs;
        if (!empty($this->fat)) $nutritionalInfo['fat'] = $this->fat;

        $mealData = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'nutritional_info' => !empty($nutritionalInfo) ? json_encode($nutritionalInfo) : null,
            'is_available' => $this->is_available,
            'preparation_time' => $this->preparation_time,
            'canteen_id' => $this->canteen->id,
        ];

        // Handle new image upload
        if ($this->image && is_object($this->image)) {
            // Delete old image if editing and has existing image
            if ($this->editingMeal && $this->editingMeal->image_url) {
                $oldImagePath = str_replace('/storage/', '', $this->editingMeal->image_url);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            // Store new image
            $imagePath = $this->image->store('meals', 'public');
            $mealData['image_url'] = Storage::url($imagePath);
        } elseif ($this->editingMeal && $this->editingMeal->image_url) {
            // Keep existing image if editing and no new image uploaded
            $mealData['image_url'] = $this->editingMeal->image_url;
        }

        if ($this->editingMeal) {
            $this->editingMeal->update($mealData);
            session()->flash('success', 'Meal updated successfully.');
        } else {
            Meal::create($mealData);
            session()->flash('success', 'Meal created successfully.');
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function deleteMeal(Meal $meal)
    {
        if ($meal->canteen_id !== $this->canteen?->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        // Check if meal has orders
        if ($meal->orders()->exists()) {
            session()->flash('error', 'Cannot delete meal with existing orders.');
            return;
        }

        // Delete image if exists
        if ($meal->image_url) {
            $imagePath = str_replace('/storage/', '', $meal->image_url);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $meal->delete();
        session()->flash('success', 'Meal deleted successfully.');
    }

    public function toggleAvailability(Meal $meal)
    {
        if ($meal->canteen_id !== $this->canteen?->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $meal->update([
            'is_available' => !$meal->is_available
        ]);

        session()->flash('success', 'Meal availability updated.');
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'description', 'price', 'category',
            'is_available', 'preparation_time',
            'image', 'image_url', 'calories', 'protein', 'carbs', 'fat'
        ]);
        $this->is_available = true;
        $this->preparation_time = 15;
        $this->editingMeal = null;
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048',
        ]);
    }
}
