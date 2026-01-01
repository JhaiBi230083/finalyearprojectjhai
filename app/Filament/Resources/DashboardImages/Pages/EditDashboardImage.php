<?php

namespace App\Filament\Resources\DashboardImages\Pages;

use App\Filament\Resources\DashboardImages\DashboardImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDashboardImage extends EditRecord
{
    protected static string $resource = DashboardImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
