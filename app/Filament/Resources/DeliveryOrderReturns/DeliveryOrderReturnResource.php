<?php

namespace App\Filament\Resources\DeliveryOrderReturns;

use App\Filament\Resources\DeliveryOrderReturns\Pages\CreateDeliveryOrderReturn;
use App\Filament\Resources\DeliveryOrderReturns\Pages\EditDeliveryOrderReturn;
use App\Filament\Resources\DeliveryOrderReturns\Pages\ListDeliveryOrderReturns;
use App\Filament\Resources\DeliveryOrderReturns\Schemas\DeliveryOrderReturnForm;
use App\Filament\Resources\DeliveryOrderReturns\Tables\DeliveryOrderReturnsTable;
use App\Models\DeliveryOrderReturn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryOrderReturnResource extends Resource
{
    protected static ?string $model = DeliveryOrderReturn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DeliveryOrderReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryOrderReturnsTable::configure($table);
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
            'index' => ListDeliveryOrderReturns::route('/'),
            'create' => CreateDeliveryOrderReturn::route('/create'),
            'edit' => EditDeliveryOrderReturn::route('/{record}/edit'),
        ];
    }
}
