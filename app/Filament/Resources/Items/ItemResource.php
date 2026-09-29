<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages;
use App\Models\Item;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
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

                TextColumn::make('minimum_stock')
                    ->label('Min. Stok')
                    ->alignCenter(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}