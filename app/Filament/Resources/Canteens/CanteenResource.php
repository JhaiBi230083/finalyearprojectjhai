<?php

namespace App\Filament\Resources\Canteens;

use App\Filament\Resources\Canteens\Pages\CreateCanteen;
use App\Filament\Resources\Canteens\Pages\EditCanteen;
use App\Filament\Resources\Canteens\Pages\ListCanteens;
use App\Filament\Resources\Canteens\Pages\ViewCanteen;
use App\Filament\Resources\Canteens\Schemas\CanteenForm;
use App\Filament\Resources\Canteens\Schemas\CanteenInfolist;
use App\Filament\Resources\Canteens\Tables\CanteensTable;
use App\Models\Canteen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CanteenResource extends Resource
{
    protected static ?string $model = Canteen::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Canteen';

    public static function form(Schema $schema): Schema
    {
        return CanteenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CanteenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CanteensTable::configure($table);
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
            'index' => ListCanteens::route('/'),
            'create' => CreateCanteen::route('/create'),
            'view' => ViewCanteen::route('/{record}'),
            'edit' => EditCanteen::route('/{record}/edit'),
        ];
    }
}
