<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryOrderOut extends Model
{
    protected $table = 'delivery_orders_out';

    protected $guarded = ['id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatcher_user_id');
    }

    public function toolItems(): HasMany
    {
        return $this->hasMany(DoOutToolItem::class, 'do_out_id');
    }

    public function consumableItems(): HasMany
    {
        return $this->hasMany(DoOutConsumableItem::class, 'do_out_id');
    }
}