<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoOutToolItem extends Model
{
    protected $table = 'do_out_tool_items';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function deliveryOrderOut(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrderOut::class, 'do_out_id');
    }

    public function toolAsset(): BelongsTo
    {
        return $this->belongsTo(ToolAsset::class, 'tool_asset_id');
    }
}