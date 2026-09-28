<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderReturn extends Model
{
    use HasFactory;

    // Paksa Laravel menggunakan nama tabel return yang benar
    protected $table = 'delivery_orders_return';

    protected $guarded = [];
}