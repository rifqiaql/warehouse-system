<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use App\Models\ConsumableStock;
use App\Models\ToolAsset;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();
        $record = $this->record;

        $warehouseId = $data['initial_warehouse_id'] ?? null;

        if (! $warehouseId) {
            return;
        }

        // Jika Consumable, masukkan ke tabel consumable_stocks
        if ($record->type === 'consumable' && ! empty($data['initial_quantity'])) {
            ConsumableStock::create([
                'item_id' => $record->id,
                'warehouse_id' => $warehouseId,
                'quantity' => (int) $data['initial_quantity'],
            ]);
        }

        // Jika Tool, daftarkan unit fisiknya ke tool_assets
        if ($record->type === 'tool' && ! empty($data['initial_asset_tag'])) {
            ToolAsset::create([
                'item_id' => $record->id,
                'current_warehouse_id' => $warehouseId,
                'asset_tag' => $data['initial_asset_tag'],
                'serial_number' => $data['initial_serial_number'] ?? null,
                'current_status' => 'available',
                'ownership_type' => 'owned',
            ]);
        }
    }
}