<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke Katalog Item (Kategori alatnya apa)
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi ke Gudang posisi alat saat ini
     */
    public function currentWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'current_warehouse_id');
    }

    /**
     * Relasi ke Vendor Sewa (jika alat sewa/rental)
     */
    public function rentalVendor(): BelongsTo
    {
        return $this->belongsTo(RentalVendor::class);
    }
}