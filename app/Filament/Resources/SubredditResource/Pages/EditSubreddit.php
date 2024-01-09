<?php

namespace App\Filament\Resources\SubredditResource\Pages;

use App\Filament\Resources\SubredditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubreddit extends EditRecord
{
    protected static string $resource = SubredditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
