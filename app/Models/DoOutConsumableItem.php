<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoOutConsumableItem extends Model
{
    protected $table = 'do_out_consumable_items';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function deliveryOrderOut(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrderOut::class, 'do_out_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}