<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function toolAssets(): HasMany
    {
        return $this->hasMany(ToolAsset::class);
    }

    public function consumableStocks(): HasMany
    {
        return $this->hasMany(ConsumableStock::class);
    }

    /**
     * Hitung total stok riil otomatis (Tool dihitung per unit fisik, Consumable dijumlahkan quantity-nya)
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->type === 'tool') {
            return $this->toolAssets()->count();
        }

        return (int) $this->consumableStocks()->sum('quantity');
    }
}