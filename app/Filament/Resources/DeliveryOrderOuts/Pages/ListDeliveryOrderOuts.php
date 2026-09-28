<?php

namespace App\Filament\Resources\DeliveryOrderOuts\Pages;

use App\Filament\Resources\DeliveryOrderOuts\DeliveryOrderOutResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryOrderOuts extends ListRecords
{
    protected static string $resource = DeliveryOrderOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
