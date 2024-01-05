<?php

namespace App\Filament\Resources\OnlyfanResource\Pages;

use App\Filament\Resources\OnlyfanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOnlyfan extends CreateRecord
{
    protected static string $resource = OnlyfanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        $data['slug'] = Str::slug($data['name']);
        $data['url'] = "https://onlyfans.com/".$data['username'];
        $data['views'] = 0;
        $data['status'] = true;

        return $data;
    }
}
