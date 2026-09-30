<?php

namespace App\Filament\Resources\ToolAssets;

use App\Filament\Resources\ToolAssets\Pages;
use App\Models\ToolAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ToolAssetResource extends Resource
{
    protected static ?string $model = ToolAsset::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->label('Tipe / Jenis Alat')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('asset_tag')
                    ->label('Kode Tag Aset / No. Registrasi')
                    ->placeholder('Contoh: AST-WLD-001')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('serial_number')
                    ->label('Nomor Seri Pabrik (Serial Number)')
                    ->placeholder('Misal: SN-882910-X'),

                Select::make('current_warehouse_id')
                    ->label('Posisi Gudang Saat Ini')
                    ->relationship('currentWarehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('current_status')
                    ->label('Kondisi / Status Fisik')
                    ->options([
                        'available' => 'Tersedia di Gudang (Ready)',
                        'in_use' => 'Sedang Dipakai di Site Proyek',
                        'maintenance' => 'Dalam Perbaikan / Rusak',
                        'lost' => 'Hilang',
                        'scrapped' => 'Afkir / Dibuang',
                    ])
                    ->default('available')
                    ->required(),

                Select::make('ownership_type')
                    ->label('Status Kepemilikan')
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
                    ->label('Catatan Kondisi / Kelengkapan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->badge()
                    ->searchable(),

                TextColumn::make('item.name')
                    ->label('Nama Alat')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('serial_number')
                    ->label('Serial No.')
                    ->searchable(),

                TextColumn::make('currentWarehouse.name')
                    ->label('Lokasi Gudang')
                    ->badge()
                    ->color('gray'),

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
            ])
            ->actions([
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
            'index' => Pages\ListToolAssets::route('/'),
            'create' => Pages\CreateToolAsset::route('/create'),
            'edit' => Pages\EditToolAsset::route('/{record}/edit'),
        ];
    }
}   