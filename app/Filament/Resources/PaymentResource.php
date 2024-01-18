<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Subscriptions & Plans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 3,
                    '2xl' => 3,
                ])
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Nombre del Usuario')
                            ->options(User::get()->pluck('name', 'id'))
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('payer_name')
                            ->label('Nombre de quien pago')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payer_email')
                            ->label('Email de quien pago')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ]),
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 3,
                    '2xl' => 3,
                ])
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Methodo de pago')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_status')
                            ->label('Estado de pago')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_id')
                            ->label('ID de pago')
                            ->required()
                            ->maxLength(255)
                    ]),
                Forms\Components\Section::make('Invoice')
                    ->description('Detalle de la compra')
                    ->icon('heroicon-m-shopping-bag')
                    ->columns([
                        'sm' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('product_name')
                            ->label('Producto')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('currency')
                            ->label('Moneda')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->label('Total')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_id')->searchable(),
                Tables\Columns\TextColumn::make('product_name')->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('USD', divideBy: 1)->searchable(),
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('payer_name')->searchable(),
                Tables\Columns\TextColumn::make('payer_email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_method')->searchable(),
                Tables\Columns\TextColumn::make('payment_status')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->searchable()->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                ->native(false)
                ->label('Usuario')
                ->options(User::get()->pluck('name','id'))
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
