<?php

namespace App\Filament\SuperAdmin\Resources\AiContentResource\Pages;

use App\Filament\SuperAdmin\Resources\AiContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiContents extends ListRecords
{
    protected static string $resource = AiContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
