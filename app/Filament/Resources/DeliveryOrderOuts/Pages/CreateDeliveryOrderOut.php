<?php

namespace App\Filament\Resources\DeliveryOrderOuts\Pages;

use App\Filament\Resources\DeliveryOrderOuts\DeliveryOrderOutResource;
use App\Models\ConsumableStock;
use App\Models\ToolAsset;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDeliveryOrderOut extends CreateRecord
{
    protected static string $resource = DeliveryOrderOutResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dispatcher_user_id'] = Auth::id() ?? 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Otomatis mutasi stok saat DO Out selesai disimpan
     */
    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        $warehouseId = $record->source_warehouse_id;

        // 1. Mutasi Tool Assets: Ubah status menjadi 'in_use' (sedang dipakai di proyek)
        foreach ($record->toolItems as $toolItem) {
            ToolAsset::where('id', $toolItem->tool_asset_id)->update([
                'current_status' => 'in_use',
            ]);
        }

        // 2. Mutasi Consumables: Potong saldo 'quantity' di gudang asal
        foreach ($record->consumableItems as $consumableItem) {
            ConsumableStock::where('warehouse_id', $warehouseId)
                ->where('item_id', $consumableItem->item_id)
                ->decrement('quantity', $consumableItem->qty_dispatched);
        }
    }
}