<?php

namespace App\Filament\Resources\DeliveryOrderOuts\Pages;

use App\Filament\Resources\DeliveryOrderOuts\DeliveryOrderOutResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryOrderOut extends EditRecord
{
    protected static string $resource = DeliveryOrderOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
