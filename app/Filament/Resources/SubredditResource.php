<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubredditResource\Pages;
use App\Filament\Resources\SubredditResource\RelationManagers;
use App\Models\Subreddit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubredditResource extends Resource
{
    protected static ?string $model = Subreddit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nombre del subreddit')->required(),
                Forms\Components\TagsInput::make('tags')->label('Tags')->required(),
                Forms\Components\Checkbox::make('verification')->label('Verificación')->helperText('Requiere verificación para realizar publicaciones?')->inline(),
                Forms\Components\Checkbox::make('status')->default(true)->inline(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('tags')->badge()->searchable(),
                Tables\Columns\ToggleColumn::make('verification'),
                Tables\Columns\ToggleColumn::make('status')
            ])
            ->filters([
                //
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
            'index' => Pages\ListSubreddits::route('/'),
            'create' => Pages\CreateSubreddit::route('/create'),
            'edit' => Pages\EditSubreddit::route('/{record}/edit'),
        ];
    }
}