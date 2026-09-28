<?php

namespace App\Filament\Resources\ToolAssets\Pages;

use App\Filament\Resources\ToolAssets\ToolAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListToolAssets extends ListRecords
{
    protected static string $resource = ToolAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
