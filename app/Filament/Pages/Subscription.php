<?php

namespace App\Filament\Pages;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;
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

use Srmklive\PayPal\Services\PayPal as PayPalClient;

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
        $paypal = new PaypalController();
        $pago = $this->form->getState();
        $plan = Plan::find($pago['plan_id']);



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

    public function save()
    {
        $paypal = new PaymentController();
        $pago = $this->form->getState();
        $plan = Plan::find($pago['plan_id']);
        $pay = $paypal->pay($plan['price']);


        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success'),
                "cancel_url" => route('cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $plan['price']
                    ]
                ]
            ]
        ]);

        // dd($response);


        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {

                    session()->put('product_id', $plan['id']);
                    session()->put('product_name', $plan['name']);
                    session()->put('quantity', 1);
                    session()->put('user_id', auth()->user()->id);

                    return redirect()->away($link['href']);
                }
            }
        } else {
            return redirect()->route('cancel');
        }



        // Notification::make()
        //     ->success()
        //     ->title('Filtrando')
        //     ->send();

        // $queryString = Arr::query($data);
        // $this->redirect('/admin/calendar?' . $queryString, navigate: true);
    }
}
