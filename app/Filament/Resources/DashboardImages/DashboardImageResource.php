<?php

namespace App\Filament\Resources\DashboardImages;

use App\Filament\Resources\DashboardImages\Pages\CreateDashboardImage;
use App\Filament\Resources\DashboardImages\Pages\EditDashboardImage;
use App\Filament\Resources\DashboardImages\Pages\ListDashboardImages;
use App\Filament\Resources\DashboardImages\Schemas\DashboardImageForm;
use App\Filament\Resources\DashboardImages\Tables\DashboardImagesTable;
use App\Models\DashboardImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DashboardImageResource extends Resource
{
    protected static ?string $model = DashboardImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'DashboardImage';

    public static function form(Schema $schema): Schema
    {
        return DashboardImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DashboardImagesTable::configure($table);
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
            'index' => ListDashboardImages::route('/'),
            'create' => CreateDashboardImage::route('/create'),
            'edit' => EditDashboardImage::route('/{record}/edit'),
        ];
    }
}
