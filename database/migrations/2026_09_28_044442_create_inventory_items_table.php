<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Vendor Sewa Alat
        Schema::create('rental_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Katalog Master Barang
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique();
            $table->string('name', 255);
            $table->string('type', 20); // consumable, tool
            $table->string('unit', 20); // Pcs, Box, Unit, Roll
            $table->integer('minimum_stock')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Stok Consumable (Agregat per Gudang)
        Schema::create('consumable_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamps();

            // 1 barang hanya boleh punya 1 baris per gudang
            $table->unique(['item_id', 'warehouse_id']);
        });

        // Aset Alat Fisik (Tracking Serialized/QR per Unit)
        Schema::create('tool_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('current_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('asset_tag', 60)->unique(); // Contoh: AST-WLD-2026-0001
            $table->string('serial_number', 100)->nullable();
            $table->string('ownership_type', 20)->default('owned'); // owned, rented
            $table->foreignId('rental_vendor_id')->nullable()->constrained('rental_vendors')->nullOnDelete();
            $table->date('rental_end_date')->nullable();
            $table->string('current_status', 30)->default('available'); // available, in_use, maintenance, scrapped, lost
            $table->text('condition_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_assets');
        Schema::dropIfExists('consumable_stocks');
        Schema::dropIfExists('items');
        Schema::dropIfExists('rental_vendors');
    }
};