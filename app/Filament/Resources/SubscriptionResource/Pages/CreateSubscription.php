<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\SubscriptionResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use LucasDotVin\Soulbscription\Models\Plan;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $subscriber = User::find($data['user_id']);
        $plan = Plan::find($data['plan_id']);
        return $subscriber->subscribeTo($plan);
        // return static::getModel()::create($data);
    }
}
