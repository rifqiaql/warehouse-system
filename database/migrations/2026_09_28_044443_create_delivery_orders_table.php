<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. DO Keluar ke Proyek
        Schema::create('delivery_orders_out', function (Blueprint $table) {
            $table->id();
            $table->string('do_number', 50)->unique();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('source_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('dispatcher_user_id')->constrained('users')->restrictOnDelete();
            $table->date('do_date');
            $table->string('recipient_name', 100)->nullable();
            $table->string('status', 30)->default('dispatched'); // dispatched, partially_returned, fully_returned
            $table->timestamps();
        });

        Schema::create('do_out_consumable_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('do_out_id')->constrained('delivery_orders_out')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->integer('qty_dispatched');
        });

        Schema::create('do_out_tool_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('do_out_id')->constrained('delivery_orders_out')->cascadeOnDelete();
            $table->foreignId('tool_asset_id')->constrained('tool_assets')->restrictOnDelete();
            $table->string('status', 30)->default('on_site'); // on_site, returned, lost_damaged
        });

        // 2. DO Return & Hasil QC/Inspeksi
        Schema::create('delivery_orders_return', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('do_out_id')->constrained('delivery_orders_out')->restrictOnDelete();
            $table->foreignId('target_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('inspector_user_id')->constrained('users')->restrictOnDelete();
            $table->date('return_date');
            $table->string('status', 30)->default('draft_inspection'); // draft_inspection, confirmed_closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('do_return_consumable_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('do_return_id')->constrained('delivery_orders_return')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->integer('qty_returned')->comment('Kembali masuk ke stok gudang');
            $table->integer('qty_consumed')->comment('Habis terpakai di proyek');
            $table->integer('qty_waste_damaged')->default(0)->comment('Rusak/afkir saat proyek');
        });

        Schema::create('do_return_tool_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('do_return_id')->constrained('delivery_orders_return')->cascadeOnDelete();
            $table->foreignId('tool_asset_id')->constrained('tool_assets')->restrictOnDelete();
            $table->string('inspection_result', 30); // good, damaged_repairable, total_loss
            $table->string('action_taken', 30);      // return_to_stock, send_to_maintenance, scrap_charge_project
            $table->text('inspection_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('do_return_tool_items');
        Schema::dropIfExists('do_return_consumable_items');
        Schema::dropIfExists('delivery_orders_return');
        Schema::dropIfExists('do_out_tool_items');
        Schema::dropIfExists('do_out_consumable_items');
        Schema::dropIfExists('delivery_orders_out');
    }
};