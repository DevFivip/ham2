<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Onlyfan;
use DateTime;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Filament\Notifications\Notification;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;
    protected int | string | array $columnSpan = 'full';
    public $widgetData;

    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Select::make('model_id')
    //                 ->label('Modelo')
    //                 ->options(
    //                     Onlyfan::where('user_id', auth()->user()->id)->get()->pluck('name', 'id')
    //                 ),
    //         ])
    //         ->statePath('data');
    // }

    // public function submit(): void
    // {
    //     $state = $this->form->getState()['model_id'];
    //     $this->dispatch('FilterSubmit', $state);
    // }

    public function mount($widgetData)
    {
        $this->widgetData = $widgetData;

        // $this->content = $post->content;
    }

    public function fetchEvents(array $fetchInfo): array
    {
        // dd($this->widgetData);

        $query = Event::where('user_id', auth()->user()->id);

        if (isset($this->widgetData["model_id"])) {
            $query->where('model_id', $this->widgetData["model_id"]);
        }

        if (isset($this->widgetData["subreddit_id"])) {
            $query->whereIn('subreddit_id', $this->widgetData["subreddit_id"]);
        }

        if (isset($this->widgetData["status"])) {
            $query->where('status', $this->widgetData["status"]);
        }

        // dd($this->xdata);
        //? dd($this->widgetData);
        return $query->get()


            // where('posted_at', '>=', $fetchInfo['posted_at'])
            // ->where('posted_at', '<=', $fetchInfo['posted_at'])
            // ->get()
            ->map(function (Event $event) {
                return [
                    'id'    => $event->id,
                    'title' => $event->full_description,
                    'start' => $event->posted_at,
                    'end'   => null,
                ];
            })
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Select::make('model_id')
                        ->label('Modelo')
                        ->options(Onlyfan::where('user_id', auth()->user()->id)->get()->pluck('name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => ('subreddit_id'))
                        ->searchable()
                        ->required(),
                    Select::make('subreddit_id')
                        ->label('Subreddit')
                        ->options(function (callable $get) {
                            $modelo = Onlyfan::find($get('model_id'));
                            if (!$modelo) {
                                return [];
                            } else {
                                return ($modelo->subreddits()->get())->pluck('full_description', 'id');
                            }
                        })
                        ->searchable()
                        ->required(),
                    Select::make('status')
                        ->options([
                            1 => 'Error',
                            2 => 'Pendiente',
                            3 => 'Publicado',
                        ])
                        ->searchable()
                        ->placeholder('Seleccione Status')->default(2)
                        ->required(),
                    DateTimePicker::make('posted_at')
                        ->label('Fecha Programada')
                        ->native(false)
                        ->required(),
                ]),
        ];
    }

    function obtenerListaSubreddits($subredditsAsignados, $numerosubreddits)
    {

        // Obtener una lista aleatoria de subreddits
        $subredditsAleatorias = array_rand($subredditsAsignados, intval($numerosubreddits));

        // Si solo se seleccionó una subreddit, convertir a array para mantener consistencia
        if (!is_array($subredditsAleatorias)) {
            $subredditsAleatorias = array($subredditsAleatorias);
        }

        // Obtener los nombres de las subreddits seleccionadas
        $subredditsSeleccionadas = array();
        foreach ($subredditsAleatorias as $indice) {
            $subredditsSeleccionadas[] = $subredditsAsignados[$indice];
        }

        return $subredditsSeleccionadas;
    }


    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    return [
                        ...$data,
                        'user_id' => auth()->user()->id
                    ];
                }),

            Action::make('scheduleEvents')
                ->label('Programar Eventos')
                ->form([
                    Select::make('model_id')
                        ->label('Modelo')
                        ->options(Onlyfan::where('user_id', auth()->user()->id)->get()->pluck('name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => ('subreddit_id'))
                        ->searchable()
                        ->required(),
                    DatePicker::make('fecha_inicio')->label('Fecha Inicio')->native(false)->required(),
                    DatePicker::make('fecha_final')->label('Fecha Fin')->native(false)->required(),
                    TextInput::make('number')->label('Post por dias')->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                    // RichEditor::make('body')->required(),
                ])
                ->action(function (array $data) {

                    // Definir las subreddits disponibles
                    $cs = Onlyfan::find($data['model_id']);
                    $subredditsAsignados = $cs->subreddits()->where('status', 1)->get()->pluck("id")->toArray();

                    // Definir el periodo de tiempo
                    $fechaInicio = new DateTime($data["fecha_inicio"]);
                    $fechaFin = new DateTime($data["fecha_final"]);
                    // dd( $fechaInicio, $fechaFin);

                    $res = [];
                    $datetime = new DateTime();
                    // Generar lista de subreddits para cada día
                    while ($fechaInicio <= $fechaFin) {
                        $fechaActual = $fechaInicio->format('Y-m-d');
                        // dd($subredditsAsignados);
                        $subredditDia = $this->obtenerListaSubreddits($subredditsAsignados, $data['number']);

                        // echo "Para el día $fechaActual, come las siguientes subreddits: " . implode(", ", $subredditDia) . "\n";
                        // dd($subredditDia);
                        foreach ($subredditDia as $subreddit) {
                            $arr = ["created_at" => $datetime, "updated_at" => $datetime, "posted_at" => $fechaActual, "subreddit_id" => $subreddit, 'user_id' => auth()->user()->id, 'model_id' => $data['model_id'], 'status' => 2];
                            error_log($subreddit);
                            array_push($res, $arr);
                        }


                        // Avanzar al siguiente día
                        $fechaInicio->modify('+1 day');
                    }

                    Event::insert($res);

                    Notification::make()
                        ->success()
                        ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                        ->send();

                    $this->js('window.location.reload()');
                }),

        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function (Event $record, Form $form, array $arguments) {
                        $form->fill([
                            ...$record->toArray(),
                            'posted_at' => $arguments['event']['start'] ?? $record->posted_at,
                            //  'ends_at' => $arguments['event']['end'] ?? $record->ends_at
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }

    public static function canView(): bool
    {
        return false;
    }
}
