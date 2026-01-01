<?php

namespace App\Filament\Resources\Queues\Pages;

use App\Filament\Resources\Queues\QueueResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQueue extends ViewRecord
{
    protected static string $resource = QueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
