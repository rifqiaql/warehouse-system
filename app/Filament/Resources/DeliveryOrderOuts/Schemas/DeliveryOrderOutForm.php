<?php

namespace App\Filament\Resources\DeliveryOrderOuts\Schemas;

use App\Models\ToolAsset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                // SECTION 1: HEADER SURAT JALAN
                Section::make('Informasi Surat Jalan (DO Out)')
                    ->description('Detail pengiriman barang dari gudang ke lokasi proyek')
                    ->columns(3)
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
                            ->live()
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

                // SECTION 2: UNIT ALAT & MESIN (NON-CONSUMABLE)
                Section::make('Daftar Alat / Mesin (Non-Consumable)')
                    ->description('Pilih unit fisik alat yang dibawa ke proyek (wajib berstatus Ready)')
                    ->components([
                        Repeater::make('toolItems')
                            ->relationship('toolItems')
                            ->schema([
                                Select::make('tool_asset_id')
                                    ->label('Pilih Unit Alat Fisik')
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
                                    ->required()
                                    ->columnSpan(2),

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
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('+ Tambah Unit Alat')
                            ->reorderable(false),
                    ]),

                // SECTION 3: MATERIAL HABIS PAKAI (CONSUMABLE)
                Section::make('Daftar Material Habis Pakai (Consumable)')
                    ->description('Material atau bahan operasional yang dikeluarkan ke proyek')
                    ->components([
                        Repeater::make('consumableItems')
                            ->relationship('consumableItems')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Nama Material / Barang')
                                    ->relationship(
                                        name: 'item',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'consumable')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('qty_dispatched')
                                    ->label('Jumlah Dikeluarkan')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('+ Tambah Material')
                            ->reorderable(false),
                    ]),
            ]);
    }
}