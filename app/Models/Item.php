<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Jika item bertipe 'tool', item ini punya banyak unit fisik (assets)
     */
    public function toolAssets(): HasMany
    {
        return $this->hasMany(ToolAsset::class);
    }

    /**
     * Riwayat item pada surat jalan keluar
     */
    public function deliveryOrderOutItems(): HasMany
    {
        return $this->hasMany(DeliveryOrderOutItem::class);
    }
}