<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OnlyfanResource\Pages;
use App\Filament\Resources\OnlyfanResource\RelationManagers;
use App\Models\Onlyfan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class OnlyfanResource extends Resource
{
    protected static ?string $model = Onlyfan::class;

    protected static ?string $navigationIcon = 'si-onlyfans';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->user()->id);
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->helperText('Tu nombre en onlyfans no es necesario tu nombre real'),
            Forms\Components\TextInput::make('username')
                ->prefix('https://onlyfans.com/')
                ->label('Onlyfans Usuario')
                ->required(),
            Section::make()
                ->columns([
                    'sm' => 4,
                    'md' => 2,
                    'xl' => 4,
                    '2xl' => 4,
                ])
                ->schema([
                    Forms\Components\TextInput::make('cant_suscriptores')
                        ->label('Cantidad de Subscriptores')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\TextInput::make('cant_fotos')
                        ->label('Cantidad de Fotos')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\TextInput::make('cant_videos')
                        ->label('Cantidad de Videos')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\TextInput::make('cant_posts')
                        ->label('Cantidad de Post')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ]),

            Forms\Components\TextInput::make('precio_membresia')
                ->label('Precio de la Subscripción a tu Onlyfans')
                ->numeric()
                ->prefix('$')
                ->inputMode('decimal')
                ->default(0)
                ->step(0.05)
                ->required()
                ->helperText('Dejar en $0 si tu cuenta es gratis'),
            Section::make()
                ->columns([
                    'sm' => 1,
                    'md' => 1,
                    'xl' => 1,
                    '2xl' => 1,
                ])
                ->schema([
                    Forms\Components\Checkbox::make('show_more_social_medias')
                        ->label('Cuentas con mas enlaces de redes sociales dentro de mi onlyfans?')
                        ->inline(),
                    Forms\Components\Checkbox::make('usuarios_comunicacion')
                        ->label('Los usuarios pueden hablar contigo directamente en tu onlyfans?')
                        ->inline(),
                ]),
            Forms\Components\RichEditor::make('description')
                ->label('Descripcion de tu biografía')
                ->toolbarButtons(['blockquote', 'bold', 'bulletList', 'italic', 'orderedList', 'redo', 'strike', 'underline', 'undo'])
                ->columnSpanFull()
                ->required(),
            Forms\Components\FileUpload::make('imagen')
                ->image()
                ->avatar()
                ->imageEditor()
                ->circleCropper()
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('banner')
                ->image()
                ->imageEditor()
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('16:9')
                ->imageResizeTargetWidth('1920')
                ->imageResizeTargetHeight('1080')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagen')->circular(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('username'),
                Tables\Columns\IconColumn::make('status')->icon(
                    fn (string $state): string => match ($state) {
                        '1' => 'heroicon-o-check-circle',
                        '0' => 'heroicon-s-x-circle',
                    },
                ),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('Subreddit Asignacion')
                        ->label('Subreddit Asignacion')
                        ->icon('fab-reddit')
                        ->url(fn (Onlyfan $record): string => route('filament.admin.resources.onlyfans.asing', $record)),
                ])
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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
            'index' => Pages\ListOnlyfans::route('/'),
            'create' => Pages\CreateOnlyfan::route('/create'),
            'edit' => Pages\EditOnlyfan::route('/{record}/edit'),
            'asing' => Pages\AsingSubreddit::route('asing-subreddit/{record}'),
        ];
    }
}
