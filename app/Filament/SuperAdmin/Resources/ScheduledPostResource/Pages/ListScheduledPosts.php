<?php

namespace App\Filament\SuperAdmin\Resources\ScheduledPostResource\Pages;

use App\Filament\SuperAdmin\Resources\ScheduledPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScheduledPosts extends ListRecords
{
    protected static string $resource = ScheduledPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
