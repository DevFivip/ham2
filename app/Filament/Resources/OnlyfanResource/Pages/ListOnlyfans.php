<?php

namespace App\Filament\Resources\OnlyfanResource\Pages;

use App\Filament\Resources\OnlyfanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOnlyfans extends ListRecords
{
    protected static string $resource = OnlyfanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
