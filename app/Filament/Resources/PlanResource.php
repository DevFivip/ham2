<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use LucasDotVin\Soulbscription\Models\Plan;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Subscriptions & Plans';

    protected static bool $shouldRegisterNavigation = true; // verifica si se muesta o no 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del Plan')
                    ->required(),
                Forms\Components\TextInput::make('periodicity')
                    ->label('Periodo')
                    ->integer()
                    ->default(1)
                    ->required(),
                Forms\Components\Select::make('periodicity_type')
                    ->options([
                        'Year' => 'Year',
                        'Month' => 'Month',
                        'Week' => 'Week',
                        'Day' => 'Day',
                    ])
                    ->native(false),
                Forms\Components\TextInput::make('grace_days')
                    ->label('Dias de Gracia')
                    ->integer()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('periodicity'),
                Tables\Columns\TextColumn::make('periodicity_type')->searchable(),
                Tables\Columns\TextColumn::make('grace_days'),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
