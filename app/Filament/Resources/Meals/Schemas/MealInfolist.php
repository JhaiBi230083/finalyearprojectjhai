<?php

namespace App\Filament\Resources\Meals\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MealInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('category'),
                IconEntry::make('is_available')
                    ->boolean(),
                ImageEntry::make('image_url'),
                TextEntry::make('preparation_time')
                    ->numeric(),
                TextEntry::make('canteen_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
