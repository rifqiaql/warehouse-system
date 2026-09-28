<?php

namespace App\Filament\Resources\DeliveryOrderReturns\Pages;

use App\Filament\Resources\DeliveryOrderReturns\DeliveryOrderReturnResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryOrderReturns extends ListRecords
{
    protected static string $resource = DeliveryOrderReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
