<?php

namespace App\Filament\SuperAdmin\Resources\Posts\Pages;

use App\Filament\SuperAdmin\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
