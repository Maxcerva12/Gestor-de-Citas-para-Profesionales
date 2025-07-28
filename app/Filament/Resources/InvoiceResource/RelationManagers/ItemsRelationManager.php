<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Brick\Money\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items de Factura';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('Producto/Servicio')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->rows(2),

                Forms\Components\TextInput::make('unit_price')
                    ->label('Precio Unitario')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('unit_price', Money::of($state, 'COP'));
                    }),

                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->minValue(1)
                    ->step(1),

                Forms\Components\TextInput::make('tax_percentage')
                    ->label('IVA (%)')
                    ->numeric()
                    ->default(19)
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Producto/Servicio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->description;
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Unit.')
                    ->money('COP')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('tax_percentage')
                    ->label('IVA')
                    ->suffix('%')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->alignEnd()
                    ->state(function ($record) {
                        return $record->unit_price->multipliedBy($record->quantity);
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->alignEnd()
                    ->state(function ($record) {
                        $subtotal = $record->unit_price->multipliedBy($record->quantity);
                        $tax = $subtotal->multipliedBy($record->tax_percentage ?? 19)->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                        return $subtotal->plus($tax);
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Item'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }
}
