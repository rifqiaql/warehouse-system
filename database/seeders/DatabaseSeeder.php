<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Warehouse;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Multi-Gudang (Gudang 1 s/d 5)
        for ($i = 1; $i <= 5; $i++) {
            Warehouse::firstOrCreate(
                ['code' => "GDG-0{$i}"],
                [
                    'name' => "Gudang {$i}",
                    'type' => 'physical',
                    'location' => "Area Workshop {$i}",
                ]
            );
        }

        // 2. Data Klien Utama
        $clients = [
            ['code' => 'PTM', 'name' => 'PT Pertamina (Persero)', 'contact_person' => 'Bpk. Hendra', 'phone' => '081234567890'],
            ['code' => 'CAP', 'name' => 'PT Chandra Asri Petrochemical', 'contact_person' => 'Ibu Maya', 'phone' => '081234567891'],
            ['code' => 'TPPI', 'name' => 'PT Trans-Pacific Petrochemical Indah', 'contact_person' => 'Bpk. Arif', 'phone' => '081234567892'],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(['code' => $client['code']], $client);
        }
    }
}