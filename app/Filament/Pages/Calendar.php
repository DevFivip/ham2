<?php

namespace App\Filament\Pages;

use Illuminate\Support\Arr;

use App\Models\Event;
use App\Models\Onlyfan;
use App\Models\Subreddit;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Filament\Forms\Form;
use Filament\Actions\Action;


use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class Calendar extends Page implements HasForms
{
    use InteractsWithForms;

    public $widgetData;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.calendar';

    static ?array $xdata = [];

    public function mount(Request $req): void
    {
        $init = $req->all();
        // dd($init['model_id']);

        if (!array_key_exists("model_id", $init)) {
            $init['model_id'] = null;
        }
        if (!array_key_exists("subreddit_id", $init)) {
            $init['subreddit_id'] = null;
        }
        if (!array_key_exists("status", $init)) {
            $init['status'] = null;
        }
        // if(!$init['subreddit_id']){
        //     $init['subreddit_id'] = null;
        // }
        // if(!$init['status']){
        //     $init['status'] = null;
        // }

        // if (count($req->all()) == 0) {
        //     $this->widgetData = ['model_id' => null, 'subreddit_id' => null, 'status' => null];
        // } else {
        $this->widgetData = $init;
        //  dd($this->widgetData);
        // }
    }
    public function form(Form $form): Form
    {
        return $form
            ->columns([
                'sm' => 6,
                'xl' => 3,
                '2xl' => 3,
            ])
            ->schema([
                Select::make('model_id')
                    ->label('Modelo')
                    ->options(Onlyfan::where('user_id', auth()->user()->id)->get()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('subreddit_id')
                    ->multiple()
                    ->label('Subreddit')
                    ->options(Subreddit::all()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('status')
                    ->options([
                        1 => 'Error',
                        2 => 'Pendiente',
                        3 => 'Publicado',
                    ])
                    ->searchable(),
            ])
            ->statePath('widgetData');
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

        Notification::make()
            ->success()
            ->title('Filtrando')
            ->send();

        $queryString = Arr::query($data);
        $this->redirect('/admin/calendar?' . $queryString, navigate: true);
    }
}
