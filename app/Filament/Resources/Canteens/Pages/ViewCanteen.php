<?php

namespace App\Filament\Resources\Canteens\Pages;

use App\Filament\Resources\Canteens\CanteenResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCanteen extends ViewRecord
{
    protected static string $resource = CanteenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
