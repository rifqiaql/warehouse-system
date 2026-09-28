<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages;
use App\Models\Project;
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

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Klien / Pemilik Proyek')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nama Proyek')
                    ->placeholder('Misal: Maintenance Kilang Balongan Unit IV')
                    ->required(),

                TextInput::make('location')
                    ->label('Lokasi Site Lapangan')
                    ->placeholder('Misal: Balongan, Indramayu')
                    ->required(),

                DatePicker::make('start_date')
                    ->label('Tanggal Mulai Proyek'),

                DatePicker::make('end_date')
                    ->label('Estimasi Selesai'),

                Select::make('status')
                    ->label('Status Proyek')
                    ->options([
                        'active' => 'Aktif / Berjalan',
                        'completed' => 'Selesai',
                        'suspended' => 'Ditunda / On Hold',
                    ])
                    ->default('active')
                    ->required(),

                Textarea::make('description')
                    ->label('Keterangan Tambahan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('client.name')
                    ->label('Klien')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Lokasi Site')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'suspended' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'completed' => 'Selesai',
                        'suspended' => 'On Hold',
                        default => $state,
                    }),

                TextColumn::make('start_date')
                    ->label('Tgl Mulai')
                    ->date('d M Y')
                    ->sortable(),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}