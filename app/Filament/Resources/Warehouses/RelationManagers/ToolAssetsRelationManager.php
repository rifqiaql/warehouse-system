<?php

namespace App\Filament\Resources\Warehouses\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ToolAssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'toolAssets';

    /**
     * Menampilkan counter otomatis di judul tab gudang
     * Contoh: "Daftar Unit Alat & Mesin (3 Unit)"
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        $count = $ownerRecord->toolAssets()->count();
        return "Daftar Unit Alat & Mesin ({$count} Unit)";
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->label('Katalog Barang / Alat')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'tool')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('item_code')
                            ->label('Kode Barang')
                            ->placeholder('Misal: TLS-WLD-02')
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Barang / Mesin')
                            ->placeholder('Misal: Trafo Las 400A')
                            ->required(),

                        Hidden::make('type')
                            ->default('tool'),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->default('Unit')
                            ->required(),

                        TextInput::make('minimum_stock')
                            ->label('Batas Minimum Stok')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),

                TextInput::make('asset_tag')
                    ->label('Kode Tag Aset')
                    ->placeholder('Contoh: AST-WLD-001')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('serial_number')
                    ->label('Nomor Seri Pabrik (SN)')
                    ->placeholder('Misal: SN-99821-X'),

                Select::make('current_status')
                    ->label('Status Kondisi')
                    ->options([
                        'available' => 'Tersedia di Gudang (Ready)',
                        'in_use' => 'Sedang Dipakai di Lapangan',
                        'maintenance' => 'Dalam Perbaikan / Rusak',
                        'lost' => 'Hilang',
                        'scrapped' => 'Afkir / Dibuang',
                    ])
                    ->default('available')
                    ->required(),

                Select::make('ownership_type')
                    ->label('Kepemilikan')
                    ->options([
                        'owned' => 'Milik Sendiri',
                        'rented' => 'Sewa Vendor (Rental)',
                    ])
                    ->default('owned')
                    ->reactive()
                    ->required(),

                DatePicker::make('rental_end_date')
                    ->label('Batas Akhir Sewa')
                    ->visible(fn ($get) => $get('ownership_type') === 'rented'),

                Textarea::make('condition_note')
                    ->label('Catatan Kondisi')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('asset_tag')
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Tag Aset')
                    ->badge()
                    ->color('warning')
                    ->searchable(),

                TextColumn::make('item.name')
                    ->label('Nama Alat / Mesin')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('item.unit')
                    ->label('Jumlah Fisik')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "1 " . ($state ?: 'Unit'))
                    ->alignCenter(),

                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->default('-'),

                TextColumn::make('current_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'in_use' => 'info',
                        'maintenance' => 'warning',
                        'lost', 'scrapped' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Ready (Gudang)',
                        'in_use' => 'Di Lapangan',
                        'maintenance' => 'Maintenance',
                        'lost' => 'Hilang',
                        'scrapped' => 'Afkir',
                        default => $state,
                    }),

                TextColumn::make('ownership_type')
                    ->label('Kepemilikan')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'owned' ? 'gray' : 'warning')
                    ->formatStateUsing(fn (string $state): string => $state === 'owned' ? 'Milik Sendiri' : 'Sewa'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Unit Alat Baru'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}