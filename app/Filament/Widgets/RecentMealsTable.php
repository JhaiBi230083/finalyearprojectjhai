<?php

namespace App\Filament\Widgets;

use App\Models\Meal;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentMealsTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recently Added Meals';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Meal::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'breakfast' => 'info',
                        'lunch' => 'success',
                        'dinner' => 'warning',
                        'snack' => 'primary',
                        'beverage' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->money('MYR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label('Available'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Added On'),
            ]);
    }
}
