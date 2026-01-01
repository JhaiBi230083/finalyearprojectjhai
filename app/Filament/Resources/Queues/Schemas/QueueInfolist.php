<?php

namespace App\Filament\Resources\Queues\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QueueInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
