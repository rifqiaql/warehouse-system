<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages;
use App\Models\Item;
use App\Models\Warehouse;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Katalog Master')
                    ->schema([
                        TextInput::make('item_code')
                            ->label('Kode Barang / Part Number')
                            ->placeholder('Misal: CNS-KWT-01 atau TLS-WLD-01')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Nama Barang / Material')
                            ->placeholder('Misal: Kawat Las LB-52 / Trafo Las 400A')
                            ->required(),

                        Select::make('type')
                            ->label('Kategori / Klasifikasi')
                            ->options([
                                'consumable' => 'Habis Pakai (Consumable)',
                                'tool' => 'Peralatan / Aset (Tool / Machine)',
                            ])
                            ->reactive()
                            ->required(),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->placeholder('Contoh: Pcs, Unit, Box, Roll, Kg')
                            ->required(),

                        TextInput::make('minimum_stock')
                            ->label('Batas Minimum Stok')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Textarea::make('description')
                            ->label('Spesifikasi / Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Inisialisasi Stok / Unit Awal')
                    ->description('Masukkan saldo stok atau unit alat awal ke gudang yang dipilih')
                    ->visibleOn('create')
                    ->schema([
                        Select::make('initial_warehouse_id')
                            ->label('Gudang Masuk')
                            ->options(fn () => Warehouse::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->dehydrated(false),

                        // Tampil jika Consumable
                        TextInput::make('initial_quantity')
                            ->label('Jumlah Stok Awal')
                            ->numeric()
                            ->placeholder('Misal: 50')
                            ->visible(fn ($get) => $get('type') === 'consumable')
                            ->dehydrated(false),

                        // Tampil jika Tool / Aset
                        TextInput::make('initial_asset_tag')
                            ->label('Kode Tag Unit Pertama')
                            ->placeholder('Contoh: AST-WLD-001')
                            ->visible(fn ($get) => $get('type') === 'tool')
                            ->dehydrated(false),

                        TextInput::make('initial_serial_number')
                            ->label('Nomor Seri Unit (SN)')
                            ->placeholder('Misal: SN-88912-X')
                            ->visible(fn ($get) => $get('type') === 'tool')
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_code')
                    ->label('Kode')
                    ->badge()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'consumable' => 'info',
                        'tool' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'consumable' => 'Consumable',
                        'tool' => 'Tool / Aset',
                        default => $state,
                    }),

                TextColumn::make('unit')
                    ->label('Satuan'),

                // Total Riil Seluruh Gudang
                TextColumn::make('total_stock')
                    ->label('Total Stok Tersedia')
                    ->badge()
                    ->color(fn (Item $record): string => $record->total_stock <= $record->minimum_stock ? 'danger' : 'success')
                    ->formatStateUsing(fn (Item $record): string => "{$record->total_stock} {$record->unit}")
                    ->alignCenter(),

                TextColumn::make('minimum_stock')
                    ->label('Min. Stok')
                    ->alignCenter(),
            ])
            ->actions([
                // Popup Rincian per Gudang
                Action::make('distribution')
                    ->label('Sebaran Gudang')
                    ->icon('heroicon-o-building-storefront')
                    ->color('info')
                    ->modalHeading(fn (Item $record): string => "Rincian Sebaran: {$record->name}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        RepeatableEntry::make('warehouse_distribution')
                            ->label('Stok Berdasarkan Lokasi Gudang')
                            ->state(function (Item $record): array {
                                $warehouses = Warehouse::orderBy('name')->get();
                                $data = [];

                                foreach ($warehouses as $wh) {
                                    if ($record->type === 'tool') {
                                        $qty = $record->toolAssets()->where('current_warehouse_id', $wh->id)->count();
                                    } else {
                                        $qty = (int) $record->consumableStocks()->where('warehouse_id', $wh->id)->value('quantity') ?? 0;
                                    }

                                    $data[] = [
                                        'warehouse_name' => $wh->name,
                                        'warehouse_code' => $wh->code,
                                        'stock_count' => "{$qty} {$record->unit}",
                                    ];
                                }

                                return $data;
                            })
                            ->schema([
                                TextEntry::make('warehouse_code')->label('Kode')->badge(),
                                TextEntry::make('warehouse_name')->label('Nama Gudang')->weight('bold'),
                                TextEntry::make('stock_count')->label('Stok Saat Ini')->badge()->color('success'),
                            ])
                            ->columns(3),
                    ]),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}