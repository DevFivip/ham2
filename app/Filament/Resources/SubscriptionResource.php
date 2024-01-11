<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use LucasDotVin\Soulbscription\Models\Plan;
use LucasDotVin\Soulbscription\Models\Subscription;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Subscriptions & Plans';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscriber.name')->label('Usuario'),
                Tables\Columns\TextColumn::make('plan.name')->label('Plan'),
                Tables\Columns\TextColumn::make('expired_at')->label('Vencimiento'),
                Tables\Columns\TextColumn::make('grace_days_ended_at')->label('Tolerancia')->toggleable(),
                Tables\Columns\TextColumn::make('started_at')->label('Iniciara')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('suppressed_at')->label('Suprimida')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('was_switched')->label('Ha Cambiado Plan')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dalated_at')->label('Eliminada')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Creado')->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
