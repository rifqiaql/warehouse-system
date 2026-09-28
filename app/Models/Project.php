<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke Client (Setiap proyek dimiliki oleh 1 Klien)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relasi ke Surat Jalan Keluar (Delivery Order Out)
     */
    public function deliveryOrdersOut(): HasMany
    {
        return $this->hasMany(DeliveryOrderOut::class);
    }

    /**
     * Relasi ke Surat Jalan Kembali (Delivery Order Return)
     */
    public function deliveryOrdersReturn(): HasMany
    {
        return $this->hasMany(DeliveryOrderReturn::class);
    }
}