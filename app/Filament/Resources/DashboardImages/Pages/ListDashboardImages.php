<?php

namespace App\Filament\Resources\DashboardImages\Pages;

use App\Filament\Resources\DashboardImages\DashboardImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDashboardImages extends ListRecords
{
    protected static string $resource = DashboardImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
