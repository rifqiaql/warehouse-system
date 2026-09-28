<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke stok consumable di semua gudang
    public function consumableStocks(): HasMany
    {
        return $this->hasMany(ConsumableStock::class);
    }

    // Relasi ke unit fisik alat (berbasis asset tag)
    public function toolAssets(): HasMany
    {
        return $this->hasMany(ToolAsset::class);
    }
}