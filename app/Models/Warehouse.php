<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function consumableStocks(): HasMany
    {
        return $this->hasMany(ConsumableStock::class);
    }

    public function toolAssets(): HasMany
    {
        return $this->hasMany(ToolAsset::class, 'current_warehouse_id');
    }
}