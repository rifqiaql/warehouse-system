<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderOut extends Model
{
    use HasFactory;

    // Paksa Laravel menggunakan nama tabel yang ada di migrasi
    protected $table = 'delivery_orders_out';

    protected $guarded = [];
}