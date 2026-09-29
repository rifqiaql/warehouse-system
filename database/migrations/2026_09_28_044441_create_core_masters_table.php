<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Klien: Pertamina, Chandra Asri, TPPI
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->timestamps();
        });

        // Proyek per Klien
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('location', 200)->nullable(); // Lokasi Site Lapangan
            $table->string('status', 30)->default('active'); // planning, active, completed, suspended
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Multi-Gudang (Gudang 1 s/d 5 & Virtual Site)
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('type', 30)->default('physical'); // physical, virtual_site
            $table->text('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
    }
};