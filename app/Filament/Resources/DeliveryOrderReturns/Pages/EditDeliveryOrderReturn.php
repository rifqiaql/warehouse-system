<?php

namespace App\Filament\Resources\DeliveryOrderReturns\Pages;

use App\Filament\Resources\DeliveryOrderReturns\DeliveryOrderReturnResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryOrderReturn extends EditRecord
{
    protected static string $resource = DeliveryOrderReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
