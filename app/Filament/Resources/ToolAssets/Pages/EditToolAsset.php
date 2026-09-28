<?php

namespace App\Filament\Resources\ToolAssets\Pages;

use App\Filament\Resources\ToolAssets\ToolAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditToolAsset extends EditRecord
{
    protected static string $resource = ToolAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
