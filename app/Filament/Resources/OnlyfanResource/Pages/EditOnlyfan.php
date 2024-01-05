<?php

namespace App\Filament\Resources\OnlyfanResource\Pages;

use App\Filament\Resources\OnlyfanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOnlyfan extends EditRecord
{
    protected static string $resource = OnlyfanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
