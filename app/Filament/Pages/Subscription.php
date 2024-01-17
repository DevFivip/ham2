<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Filament\Pages\Page;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use LucasDotVin\Soulbscription\Models\Plan;

class Subscription extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.subscription';

    public ?array $xdata = [];

    public ?array $init;

    public function mount(): void
    {
        // abort_unless((auth()->user())->hasFeature('subscription-module'), 403);
        $this->init = ['plan_id' => null, 'pago_id' => null];
    }

    public function getSubheading(): ?string
    {
        if (!auth()->user()->subscription) {
            return __('No posees subcripción');
        } else {
            return __('Subscripcion actual ' . auth()->user()->subscription->plan->name);
        }
    }

    // public function getMaxContentWidth(): MaxWidth
    // {
    //     return MaxWidth::Full;
    // }

    public function form(Form $form): Form
    {
        if (!auth()->user()->subscription) {
            return $form
                ->live()
                ->schema([
                    Wizard::make([
                        Wizard\Step::make('Adquirir Plan')
                            ->schema([
                                Select::make('plan_id')
                                    ->label('Plan')
                                    ->options(Plan::whereNotIn('id', [6, 5])->pluck('name', 'id'))
                                    ->native(false),
                            ]),
                        Wizard\Step::make('Delivery')
                            ->schema([
                                TextInput::make('name2')
                            ]),
                        Wizard\Step::make('Billing')
                            ->schema([
                                TextInput::make('name3')
                            ]),
                    ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Proceder orden
                </x-filament::button>
            BLADE)))
                ])
                // ->model($this->record)
                ->statePath('init')
                ->operation('edit');
        } else {
            return $form
                ->live()
                ->schema([
                    Wizard::make([
                        Wizard\Step::make(' Aumentar / Cambiar Plan')
                            ->schema([
                                Select::make('plan_id')
                                    ->label('Plan')
                                    ->options(Plan::whereNotIn('id', [auth()->user()->subscription->plan->id])->pluck('name', 'id'))
                                    ->native(false),
                            ]),
                        Wizard\Step::make('Pago')
                            ->schema([
                                TextInput::make('pago_id')
                            ]),
                        Wizard\Step::make('Finalizar Orden')
                            ->schema([
                                TextInput::make('name3')
                            ]),
                    ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Proceder orden
                </x-filament::button>
            BLADE)))
                ])
                // ->model($this->record)
                ->statePath('init')
                ->operation('edit');
        }
    }

    public function submit()
    {
        $players = $this->form->getState();
        dd($players);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('search')
                ->label('Filtrar Busqueda')
                ->submit('save')
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        dd($data);
        // Notification::make()
        //     ->success()
        //     ->title('Filtrando')
        //     ->send();

        // $queryString = Arr::query($data);
        // $this->redirect('/admin/calendar?' . $queryString, navigate: true);
    }
}
