<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use App\Models\Onlyfan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    Forms\Components\Select::make('model_id')
                        ->label('Modelo')
                        ->options(Onlyfan::where('user_id', auth()->user()->id)->get()->pluck('name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => ('subreddit_id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('subreddit_id')
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
                    Forms\Components\Select::make('status')
                        ->options([
                            1 => 'Error',
                            2 => 'Pendiente',
                            3 => 'Publicado',
                        ])
                        ->searchable()
                        ->placeholder('Seleccione Status')->default(2)
                        ->required(),
                    Forms\Components\DateTimePicker::make('posted_at')
                        ->label('Fecha Programada')
                        ->native(false)
                        ->required(),
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('onlyfan.name')->searchable(),
                Tables\Columns\TextColumn::make('subreddit.name')->searchable(),
                Tables\Columns\TextColumn::make('posted_at')->label('Postear')->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        1 => 'Error',
                        2 => 'Pendiente',
                        3 => 'Publicado',
                    ])->selectablePlaceholder(false)
                // Tables\Columns\TextColumn::make('tags')->badge()->searchable(),
                // Tables\Columns\ToggleColumn::make('verification'),
                // Tables\Columns\ToggleColumn::make('status')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        1 => 'Error',
                        2 => 'Pendiente',
                        3 => 'Publicado',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
