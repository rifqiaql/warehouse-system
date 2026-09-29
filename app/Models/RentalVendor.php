<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalVendor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function toolAssets(): HasMany
    {
        return $this->hasMany(ToolAsset::class);
    }
}