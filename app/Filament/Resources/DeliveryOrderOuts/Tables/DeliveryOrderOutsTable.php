<?php

namespace App\Filament\Resources\DeliveryOrderOuts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeliveryOrderOutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('do_date', 'desc')
            ->columns([
                TextColumn::make('do_number')
                    ->label('No. Surat Jalan')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sourceWarehouse.name')
                    ->label('Gudang Asal')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project.name')
                    ->label('Proyek Site Tujuan')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('recipient_name')
                    ->label('Penerima (Site)')
                    ->searchable(),

                TextColumn::make('tool_items_count')
                    ->counts('toolItems')
                    ->label('Unit Alat')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('consumable_items_count')
                    ->counts('consumableItems')
                    ->label('Item Material')
                    ->badge()
                    ->color('info'),

                TextColumn::make('do_date')
                    ->label('Tgl Keluar')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dispatched' => 'info',
                        'partially_returned' => 'warning',
                        'fully_returned' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dispatched' => 'Terkirim / Jalan',
                        'partially_returned' => 'Sebagian Kembali',
                        'fully_returned' => 'Selesai & Kembali',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('source_warehouse_id')
                    ->label('Filter Gudang Asal')
                    ->relationship('sourceWarehouse', 'name'),

                SelectFilter::make('project_id')
                    ->label('Filter Proyek')
                    ->relationship('project', 'name'),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'dispatched' => 'Terkirim / Jalan',
                        'partially_returned' => 'Sebagian Kembali',
                        'fully_returned' => 'Selesai & Kembali',
                    ]),
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
}