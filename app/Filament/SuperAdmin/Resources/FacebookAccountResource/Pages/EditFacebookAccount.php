<?php

namespace App\Filament\SuperAdmin\Resources\FacebookAccountResource\Pages;

use App\Filament\SuperAdmin\Resources\FacebookAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacebookAccount extends EditRecord
{
    protected static string $resource = FacebookAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
