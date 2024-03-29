<?php

namespace App\Filament\Pages;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;
use App\Models\Payment;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Filament\Pages\Page;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use App\Models\Plan;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

use Illuminate\Http\Request;


class Subscription extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.subscription';

    public ?array $xdata = [];

    public ?array $init;

    public function mount(Request $req): void
    {

        $status = $req->all();


        if (array_key_exists('status', $status)) {
            $statusValue = $status['status'];
            if ($statusValue === 'success') {
                Notification::make()
                    ->success()
                    ->title('Completado')
                    ->body('Tu pago se realizo correctamente')
                    ->persistent()
                    ->send();
            } elseif ($statusValue === 'error') {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('No se completo la compra verifica tu balance y si el problema persiste comunicate con nosotros')
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('Desconocido')
                    ->body('Error 404')
                    ->seconds(1)
                    ->send();
            }
        } else {
            // Manejar el caso en el que 'status' no está presente en el array

        }


        // dd($status);
        // abort_unless((auth()->user())->hasFeature('subscription-module'), 403);
        $this->init = ['plan_id' => null, 'pago_id' => null];
    }

    // public function getSubheading(): ?string
    // {
    //     if (!auth()->user()->subscription) {
    //         return __('No posees subcripción');
    //     } else {
    //         return __('Subscripcion actual ' . auth()->user()->subscription->plan->name);
    //     }
    // }

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
                        ->options(Plan::whereNotIn('id', [6, 5])->get()->pluck('name_with_price', 'id'))
                        ->native(false)->columnSpan(2)->required(),


                    // Select::make('plan_id')
                    // ->label('Plan')
                    // ->placeholder('Seleccione un Plan')
                    // ->options(Plan::whereNotIn('id', [6, 5])->get()->pluck('name_with_price', 'id'))
                    // ->native(false)
                    // ->required()
                    // ->columnSpan(2),
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

    public function table(Table $table): Table
    {
        return $table
            ->query(Payment::query()->where('user_id', auth()->user()->id))
            ->heading('Pagos realizados')
            ->columns([
                TextColumn::make('payment_id')->searchable(),
                TextColumn::make('product_name')->searchable(),
                TextColumn::make('amount')->money('USD', divideBy: 1)->searchable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('payer_name')->searchable(),
                TextColumn::make('payer_email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_method')->searchable(),
                TextColumn::make('payment_status')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Fecha de Pago')->searchable()->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
