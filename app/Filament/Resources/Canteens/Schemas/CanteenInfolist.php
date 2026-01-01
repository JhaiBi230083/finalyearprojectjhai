<?php

namespace App\Filament\Resources\Canteens\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CanteenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('location'),
                TextEntry::make('opening_time')
                    ->time(),
                TextEntry::make('closing_time')
                    ->time(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('vendor_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
