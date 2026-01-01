<?php

namespace App\Filament\Resources\Canteens\Pages;

use App\Filament\Resources\Canteens\CanteenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCanteens extends ListRecords
{
    protected static string $resource = CanteenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
