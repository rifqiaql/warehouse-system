<?php

namespace App\Filament\Resources\DeliveryOrderOuts\Schemas;

use App\Models\ConsumableStock;
use App\Models\ToolAsset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DeliveryOrderOutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. INFORMASI SURAT JALAN (MEMBENTANG PANJANG DI ATAS)
                Section::make('Informasi Surat Jalan (DO Out)')
                    ->description('Detail pengiriman barang dari gudang ke lokasi proyek')
                    ->columns(3)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('do_number')
                            ->label('Nomor DO / Surat Jalan')
                            ->default(fn () => 'DO-OUT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)))
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('source_warehouse_id')
                            ->label('Gudang Asal')
                            ->relationship('sourceWarehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->live() // Memastikan repeater di bawah langsung bereaksi saat gudang diubah
                            ->required(),

                        Select::make('project_id')
                            ->label('Proyek / Site Tujuan')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('do_date')
                            ->label('Tanggal Pengeluaran')
                            ->default(now())
                            ->required(),

                        TextInput::make('recipient_name')
                            ->label('Nama Penerima (Site PIC)')
                            ->placeholder('Contoh: Mandor Budi / Bpk. Arif')
                            ->required(),

                        Select::make('status')
                            ->label('Status DO')
                            ->options([
                                'dispatched' => 'Terkirim / Berjalan (Dispatched)',
                                'partially_returned' => 'Sebagian Kembali',
                                'fully_returned' => 'Semua Selesai & Kembali',
                            ])
                            ->default('dispatched')
                            ->required(),

                        Hidden::make('dispatcher_user_id')
                            ->default(fn () => Auth::id()),
                    ]),

                // 2. BAGIAN BAWAH DIBAGI JADI 2 KOLOM (KIRI ALAT, KANAN MATERIAL)
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        // KOLOM KIRI: ALAT & MESIN
                        Section::make('Daftar Alat / Mesin (Non-Consumable)')
                            ->description('Unit fisik alat yang dibawa ke proyek (wajib Ready)')
                            ->schema([
                                Repeater::make('toolItems')
                                    ->relationship('toolItems')
                                    ->schema([
                                        Select::make('tool_asset_id')
                                            ->label('Pilih Unit Alat')
                                            ->relationship(
                                                name: 'toolAsset',
                                                titleAttribute: 'asset_tag',
                                                modifyQueryUsing: function (Builder $query, $get) {
                                                    $warehouseId = $get('../../source_warehouse_id');

                                                    return $query->where('current_status', 'available')
                                                        ->when($warehouseId, fn ($q) => $q->where('current_warehouse_id', $warehouseId));
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (ToolAsset $record) => "{$record->asset_tag} - {$record->item->name} (SN: {$record->serial_number})")
                                            ->searchable()
                                            ->preload()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->required(),

                                        Select::make('status')
                                            ->label('Status di Site')
                                            ->options([
                                                'on_site' => 'On Site',
                                                'returned' => 'Returned',
                                                'lost_damaged' => 'Lost / Damaged',
                                            ])
                                            ->default('on_site')
                                            ->required(),
                                    ])
                                    ->columns(1)
                                    ->defaultItems(0)
                                    ->addActionLabel('+ Tambah Unit Alat')
                                    ->reorderable(false),
                            ]),

                        // KOLOM KANAN: MATERIAL CONSUMABLE
                        Section::make('Daftar Material Habis Pakai (Consumable)')
                            ->description('Material operasional yang dikeluarkan ke proyek')
                            ->schema([
                                Repeater::make('consumableItems')
                                    ->relationship('consumableItems')
                                    ->schema([
                                        Select::make('item_id')
                                            ->label('Nama Material / Barang')
                                            ->options(function ($get) {
                                                $warehouseId = $get('../../source_warehouse_id');

                                                if (!$warehouseId) {
                                                    return [];
                                                }

                                                return ConsumableStock::query()
                                                    ->where('warehouse_id', $warehouseId)
                                                    ->where('quantity', '>', 0)
                                                    ->with('item')
                                                    ->get()
                                                    ->mapWithKeys(function ($stock) {
                                                        $name = $stock->item?->name ?? 'Barang';
                                                        $unit = $stock->item?->unit ?? 'satuan';
                                                        return [
                                                            $stock->item_id => "{$name} (Stok: {$stock->quantity} {$unit})",
                                                        ];
                                                    });
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->required(),

                                        TextInput::make('qty_dispatched')
                                            ->label('Jumlah Dikeluarkan')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(function ($get) {
                                                $warehouseId = $get('../../source_warehouse_id');
                                                $itemId = $get('item_id');

                                                if (!$warehouseId || !$itemId) {
                                                    return 1;
                                                }

                                                return ConsumableStock::where('warehouse_id', $warehouseId)
                                                    ->where('item_id', $itemId)
                                                    ->value('quantity') ?? 1;
                                            })
                                            ->default(1)
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('+ Tambah Material')
                                    ->reorderable(false),
                            ]),
                    ]),
            ]);
    }
}