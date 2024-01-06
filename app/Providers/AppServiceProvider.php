<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make('Analytics')
                    ->url('#')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Reports')
                    ->sort(3),
                // NavigationItem::make('Test Can permision')
                //     ->icon('heroicon-o-presentation-chart-line')
                //     ->url('#')
                //     ->visible(auth()->user()->can('admin.onlyfans'))
                // // or
                // ->hidden(!auth()->user()->can('view-analytics'))
                // ,




            ]);
        });
    }
}
