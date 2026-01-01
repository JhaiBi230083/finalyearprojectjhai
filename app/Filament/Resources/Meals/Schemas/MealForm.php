<?php

namespace App\Filament\Resources\Meals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('category')
                    ->required(),
                Textarea::make('nutritional_info')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_available')
                    ->required(),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('preparation_time')
                    ->required()
                    ->numeric(),
                TextInput::make('canteen_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
