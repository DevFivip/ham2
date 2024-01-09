<?php

namespace App\Filament\Resources\SubredditResource\Pages;

use App\Filament\Resources\SubredditResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubreddit extends CreateRecord
{
    protected static string $resource = SubredditResource::class;
}
