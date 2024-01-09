<?php

namespace App\Filament\Resources\SubredditResource\Pages;

use App\Filament\Resources\SubredditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubreddits extends ListRecords
{
    protected static string $resource = SubredditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
