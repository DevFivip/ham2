<?php

namespace App\Filament\Resources\OnlyfanResource\Pages;


use App\Filament\Resources\OnlyfanResource;
use App\Models\Onlyfan;
use App\Models\Subreddit;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;

class AsingSubreddit extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [''];

    protected static string $resource = OnlyfanResource::class;

    protected static string $view = 'filament.resources.onlyfan-resource.pages.asing-subreddit';

    public Onlyfan $record;
    public $options;
    public $tags;

    public function mount(): void
    {
        $col = collect(Subreddit::where('status', 1)->get())->pluck('tags', 'id');
        static::authorizeResourceAccess();
        $this->options = (Subreddit::where('status', 1)->get())->pluck('name', 'id');
        $this->tags = $col->map(function ($arr) {
            return implode(', ', $arr);
        });
        $this->form->fill(['name' => $this->record->name]);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->disabled()
                    ->required(),
                CheckboxList::make('subreddits')->label('Subreddits Asignados')
                    ->options($this->options)
                    ->descriptions($this->tags)
                    ->searchable()
                    ->relationship(titleAttribute: 'name')->columns(3)
            ])
            ->statePath('data')
            ->model($this->record);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }
    public function save(): void
    {
        try {
            $data = $this->form->getState();
            // dd($data);
            // auth()->user()->company->update($data);
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}
