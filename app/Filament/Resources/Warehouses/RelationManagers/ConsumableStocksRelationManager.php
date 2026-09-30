<?php

namespace App\Filament\Resources\Warehouses\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsumableStocksRelationManager extends RelationManager
{
    protected static string $relationship = 'consumableStocks';

    protected static ?string $title = 'Stok Material & Habis Pakai (Bulk/Jumlah)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->label('Pilih Material / Consumable')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'consumable')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('quantity')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->placeholder('Misal: 100')
                    ->minValue(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item.name')
            ->columns([
                TextColumn::make('item.item_code')
                    ->label('Kode')
                    ->badge()
                    ->color('warning')
                    ->searchable(),

                TextColumn::make('item.name')
                    ->label('Nama Barang')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Jumlah Stok')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger')
                    ->alignCenter(),

                TextColumn::make('item.unit')
                    ->label('Satuan')
                    ->alignCenter(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Stok Material'),
            ])
            ->actions([
                EditAction::make()->label('Ubah Qty'),
                DeleteAction::make(),
            ]);
    }
}