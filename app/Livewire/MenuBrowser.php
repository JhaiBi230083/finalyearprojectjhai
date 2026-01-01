<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Meal;
use App\Models\Canteen;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class MenuBrowser extends Component
{
    use WithPagination;

    // Use Livewire 3 URL attributes for query string
    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $category = '';

    #[Url(except: '')]
    public $canteenId = '';

    #[Url(except: 'name')]
    public $sortBy = 'name';

    #[Url(except: 'asc')]
    public $sortDirection = 'asc';

    public function render()
    {
        $meals = Meal::with('canteen')
            ->available()
            ->when($this->search, function ($query) {
                $search = $this->search;
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($this->category, function ($query) {
                return $query->where('category', $this->category);
            })
            ->when($this->canteenId, function ($query) {
                return $query->where('canteen_id', $this->canteenId);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $canteens = Canteen::where('is_active', true)->get();
        $categories = Meal::distinct()->whereNotNull('category')->pluck('category');

        return view('livewire.menu-browser', compact('meals', 'canteens', 'categories'));
    }

    // Auto-reset page when filters change
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedCanteenId()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedSortDirection()
    {
        $this->resetPage();
    }

    // Toggle sort direction
    public function toggleSortDirection()
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->resetPage();
    }

    // Change sort field
    public function changeSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    // Clear all filters
    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->canteenId = '';
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }
}
