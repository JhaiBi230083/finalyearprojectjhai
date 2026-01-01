<?php

namespace App\Filament\Resources\Queues;

use App\Filament\Resources\Queues\Pages\CreateQueue;
use App\Filament\Resources\Queues\Pages\EditQueue;
use App\Filament\Resources\Queues\Pages\ListQueues;
use App\Filament\Resources\Queues\Pages\ViewQueue;
use App\Filament\Resources\Queues\Schemas\QueueForm;
use App\Filament\Resources\Queues\Schemas\QueueInfolist;
use App\Filament\Resources\Queues\Tables\QueuesTable;
use App\Models\Queue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QueueResource extends Resource
{
    protected static ?string $model = Queue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Queue';

    public static function form(Schema $schema): Schema
    {
        return QueueForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QueueInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QueuesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQueues::route('/'),
            'create' => CreateQueue::route('/create'),
            'view' => ViewQueue::route('/{record}'),
            'edit' => EditQueue::route('/{record}/edit'),
        ];
    }
}
