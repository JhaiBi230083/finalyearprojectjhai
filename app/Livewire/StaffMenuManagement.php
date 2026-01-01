<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Meal;
use App\Models\Canteen;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class StaffMenuManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $editingMeal = null;
    public $image;

    // Form fields
    public $name = '';
    public $description = '';
    public $price = '';
    public $category = '';
    public $nutritional_info = '';
    public $is_available = true;
    public $preparation_time = 15;
    public $image_url = '';
    public $canteen_id = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'category' => 'required|string|max:255',
        'nutritional_info' => 'nullable|string',
        'is_available' => 'boolean',
        'preparation_time' => 'required|integer|min:1',
        'canteen_id' => 'required|exists:canteens,id',
        'image' => 'nullable|image|max:2048',
    ];

    public function render()
    {
        $meals = Meal::with('canteen')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $canteens = Canteen::where('is_active', true)->get();
        $categories = Meal::distinct()->whereNotNull('category')->pluck('category');

        return view('livewire.staff-menu-management', compact('meals', 'canteens', 'categories'));
    }

    public function createMeal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editingMeal = null;
    }

    public function editMeal(Meal $meal)
    {
        $this->editingMeal = $meal;
        $this->name = $meal->name;
        $this->description = $meal->description;
        $this->price = $meal->price;
        $this->category = $meal->category;
        $this->nutritional_info = $meal->nutritional_info;
        $this->is_available = $meal->is_available;
        $this->preparation_time = $meal->preparation_time;
        $this->canteen_id = $meal->canteen_id;
        $this->image_url = $meal->image_url;
        $this->showModal = true;
    }

    public function saveMeal()
    {
        $this->validate();

        // Handle image upload
        if ($this->image) {
            // Delete old image if exists
            if ($this->editingMeal && $this->editingMeal->image_url) {
                $oldImagePath = str_replace('/storage/', '', $this->editingMeal->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }

            // Store new image and get relative path
            $imagePath = $this->image->store('meals', 'public');
            $this->image_url = '/storage/' . $imagePath; // Consistent format
        }

        $mealData = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'nutritional_info' => $this->nutritional_info,
            'is_available' => $this->is_available,
            'preparation_time' => $this->preparation_time,
            'canteen_id' => $this->canteen_id,
        ];

        if ($this->image_url) {
            $mealData['image_url'] = $this->image_url;
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
        // Check if meal has orders
        if ($meal->orders()->exists()) {
            session()->flash('error', 'Cannot delete meal with existing orders.');
            return;
        }

        $meal->delete();
        session()->flash('success', 'Meal deleted successfully.');
    }

    public function toggleAvailability(Meal $meal)
    {
        $meal->update([
            'is_available' => !$meal->is_available
        ]);

        session()->flash('success', 'Meal availability updated.');
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'description', 'price', 'category',
            'nutritional_info', 'is_available', 'preparation_time',
            'canteen_id', 'image', 'image_url'
        ]);
        $this->editingMeal = null;
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048',
        ]);
    }
}
