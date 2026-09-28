<?php

namespace App\Filament\Resources\ToolAssets;

use App\Filament\Resources\ToolAssets\Pages\CreateToolAsset;
use App\Filament\Resources\ToolAssets\Pages\EditToolAsset;
use App\Filament\Resources\ToolAssets\Pages\ListToolAssets;
use App\Filament\Resources\ToolAssets\Schemas\ToolAssetForm;
use App\Filament\Resources\ToolAssets\Tables\ToolAssetsTable;
use App\Models\ToolAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ToolAssetResource extends Resource
{
    protected static ?string $model = ToolAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ToolAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ToolAssetsTable::configure($table);
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
            'index' => ListToolAssets::route('/'),
            'create' => CreateToolAsset::route('/create'),
            'edit' => EditToolAsset::route('/{record}/edit'),
        ];
    }
}
