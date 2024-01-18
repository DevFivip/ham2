<?php

namespace App\Filament\Pages;

use App\Http\Controllers\PaymentController;
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
use App\Models\Plan;
use Filament\Forms\Components\Radio;

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
        if (!!auth()->user()->subscription) {
            return $form
                ->live()
                ->schema([
                    Select::make('plan_id')
                        ->placeholder('Seleccione un Plan')
                        ->label('Plan')
                        ->options(Plan::whereNotIn('id', [auth()->user()->subscription->plan->id])->get()->pluck('name_with_price', 'id'))
                        ->native(false)->columnSpan(2),
                ])
                // ->model($this->record)
                ->statePath('init')
                ->operation('edit');
        } else {
            return $form
                ->live()
                ->schema([
                    Select::make('plan_id')
                        ->label('Plan')
                        ->placeholder('Seleccione un Plan')
                        ->options(Plan::whereNotIn('id', [6, 5])->get()->pluck('name_with_price', 'id'))
                        ->native(false)
                        ->required()
                        ->columnSpan(2),
                    Radio::make('payment_id')
                        ->label('Metodo de Pago')
                        ->required()
                        ->options([
                            1 => 'Paypal',
                            2 => 'Binance',
                        ]),
                ])
                // ->model($this->record)
                ->statePath('init')
                ->operation('edit');
        }
    }

    public function submit()
    {
        $paypal = new PaymentController();
        $pago = $this->form->getState();
        $plan = Plan::find($pago['plan_id']);
        $response = $paypal->pay($plan['price']);
        error_log('$response');
        dd('QLQ',$response);
        $response->redirect();
        // dd($pay);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('search')
                ->label('Procesar Pago')
                ->submit('save')
        ];
    }

    public function save(): void
    {
        $paypal = new PaymentController();
        $pago = $this->form->getState();
        $plan = Plan::find($pago['plan_id']);
        $pay = $paypal->pay($plan['price']);
        
        // Notification::make()
        //     ->success()
        //     ->title('Filtrando')
        //     ->send();

        // $queryString = Arr::query($data);
        // $this->redirect('/admin/calendar?' . $queryString, navigate: true);
    }
}
