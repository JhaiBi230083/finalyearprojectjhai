<?php

namespace App\Filament\Resources\Canteens\Pages;

use App\Filament\Resources\Canteens\CanteenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCanteen extends EditRecord
{
    protected static string $resource = CanteenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
