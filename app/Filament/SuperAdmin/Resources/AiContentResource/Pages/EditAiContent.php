<?php

namespace App\Filament\SuperAdmin\Resources\AiContentResource\Pages;

use App\Filament\SuperAdmin\Resources\AiContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiContent extends EditRecord
{
    protected static string $resource = AiContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
