<?php

namespace App\Filament\Resources\Warehouses;

use App\Filament\Resources\Warehouses\Pages;
use App\Filament\Resources\Warehouses\RelationManagers;
use App\Models\Warehouse;
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

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Gudang')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->label('Nama Gudang')
                    ->required(),

                Select::make('type')
                    ->label('Tipe Gudang')
                    ->options([
                        'physical' => 'Fisik (Workshop/Gudang Utama)',
                        'virtual_site' => 'Virtual (Site Lapangan)',
                    ])
                    ->default('physical')
                    ->required(),

                Textarea::make('location')
                    ->label('Lokasi / Alamat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Gudang')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'physical' => 'success',
                        'virtual_site' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'physical' => 'Fisik',
                        'virtual_site' => 'Virtual Site',
                        default => $state,
                    }),

                TextColumn::make('tool_assets_count')
                    ->label('Aset Alat / Mesin')
                    ->counts('toolAssets')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "{$state} Unit")
                    ->alignCenter(),

                TextColumn::make('consumable_stocks_count')
                    ->label('Material Habis Pakai')
                    ->counts('consumableStocks')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => "{$state} Jenis")
                    ->alignCenter(),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->limit(50),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ToolAssetsRelationManager::class,
            RelationManagers\ConsumableStocksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}