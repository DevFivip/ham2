<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LucasDotVin\Soulbscription\Models\Feature;
use LucasDotVin\Soulbscription\Models\Plan;

class EditPlan extends EditRecord
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $features = Plan::find($data['id'])->features()->get();
        $f = $features->map(function ($v, $k) {
            // dd($v);
            return $v->pivot->toArray();
        });

        // dd($f);
        $data['features'] = $f;

        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // dd($data);
        $record->update($data);

        DB::delete('delete from feature_plan where plan_id = ?', [$record->id]);

        foreach ($data['features'] as $key => $value) {
            // dd($value);
            $feature = Feature::find($value['feature_id']);
            if ($value['charges'] >= 1) {
                $record->features()->attach($feature, ['charges' => $value['charges']]);
            } else {
                $record->features()->attach($feature);
            }
        }


        return $record;
    }
}
