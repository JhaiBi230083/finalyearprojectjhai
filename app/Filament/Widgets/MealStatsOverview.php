<?php

namespace App\Filament\Widgets;

use App\Models\Meal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MealStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalMeals = Meal::count();
        $availableMeals = Meal::where('is_available', true)->count();
        $unavailableMeals = Meal::where('is_available', false)->count();

        return [
            Stat::make('Total Meals', $totalMeals)
                ->description('All meals in system')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Available Meals', $availableMeals)
                ->description('Ready to order')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Unavailable Meals', $unavailableMeals)
                ->description('Currently not available')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Availability Rate', $totalMeals > 0 ? round(($availableMeals / $totalMeals) * 100, 1) . '%' : '0%')
                ->description('Percentage of available meals')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('info'),
        ];
    }
}
