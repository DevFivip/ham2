<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Onlyfan;
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

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::get()
            // where('posted_at', '>=', $fetchInfo['posted_at'])
            // ->where('posted_at', '<=', $fetchInfo['posted_at'])
            // ->get()
            ->map(function (Event $event) {
                $su = $event->subreddit;
                $mo = $event->model;

                // var_dump($mo[0]->name);

                // if(!!!$mo->name){
                //     dd($su);
                // }else{
                //     dd($mo);
                // }
                return [
                    'id'    => $event->id,
                    // 'title' => $su->name.' '. ' (' . implode(', ', $su->tags) . ")",
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
    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    return [
                        ...$data,
                        'user_id' => auth()->user()->id
                    ];
                })
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
