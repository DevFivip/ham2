<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActualPlanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // si poseer plan

        if (!!auth()->user()->subscription) {

            return [
                Stat::make('Plan Actual', auth()->user()->subscription->plan->name)
                    ->icon('polaris-major-billing-statement-dollar-filled')
                    ->description('Vencimiento:' . ' ' . auth()->user()->subscription->expired_at)
                    ->descriptionIcon('heroicon-o-calendar-days')
                    ->color('success'),
            ];
        } else {
            // no poseer plan
            return [
                Stat::make('Plan Actual', 'Sin subscripcion')
                    ->icon('polaris-major-billing-statement-dollar-filled')
                    ->descriptionColor('danger'),
            ];
        }
    }
}
