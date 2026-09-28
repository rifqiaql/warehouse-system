<?php

namespace App\Filament\Resources\DeliveryOrderOuts;

use App\Filament\Resources\DeliveryOrderOuts\Pages\CreateDeliveryOrderOut;
use App\Filament\Resources\DeliveryOrderOuts\Pages\EditDeliveryOrderOut;
use App\Filament\Resources\DeliveryOrderOuts\Pages\ListDeliveryOrderOuts;
use App\Filament\Resources\DeliveryOrderOuts\Schemas\DeliveryOrderOutForm;
use App\Filament\Resources\DeliveryOrderOuts\Tables\DeliveryOrderOutsTable;
use App\Models\DeliveryOrderOut;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryOrderOutResource extends Resource
{
    protected static ?string $model = DeliveryOrderOut::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DeliveryOrderOutForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryOrderOutsTable::configure($table);
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
            'index' => ListDeliveryOrderOuts::route('/'),
            'create' => CreateDeliveryOrderOut::route('/create'),
            'edit' => EditDeliveryOrderOut::route('/{record}/edit'),
        ];
    }
}
