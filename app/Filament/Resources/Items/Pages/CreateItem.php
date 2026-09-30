<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use App\Models\ConsumableStock;
use App\Models\ToolAsset;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    /**
     * Redirect kembali ke halaman tabel list items setelah selesai create
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Ambil data mentah form input (termasuk field yang di-set dehydrated(false))
        $data = $this->data ?? $this->form->getRawState();
        $record = $this->record;

        $warehouseId = $data['initial_warehouse_id'] ?? null;

        if (! $warehouseId) {
            return;
        }

        // 1. Jika Consumable: update atau create saldo awal di consumable_stocks
        if ($record->type === 'consumable' && ! empty($data['initial_quantity'])) {
            $stock = ConsumableStock::firstOrNew([
                'item_id' => $record->id,
                'warehouse_id' => $warehouseId,
            ]);

            $stock->quantity = ($stock->quantity ?? 0) + (int) $data['initial_quantity'];
            $stock->save();
        }

        // 2. Jika Tool: daftarkan unit mesin fisik pertamanya ke tool_assets
        if ($record->type === 'tool' && ! empty($data['initial_asset_tag'])) {
            ToolAsset::create([
                'item_id' => $record->id,
                'current_warehouse_id' => $warehouseId,
                'asset_tag' => trim($data['initial_asset_tag']),
                'serial_number' => ! empty($data['initial_serial_number']) ? trim($data['initial_serial_number']) : null,
                'current_status' => 'available',
                'ownership_type' => 'owned',
            ]);
        }
    }
}