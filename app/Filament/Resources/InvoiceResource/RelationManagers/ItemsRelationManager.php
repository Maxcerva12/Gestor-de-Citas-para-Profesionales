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

    protected static ?string $title = 'Servicios Prestados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('Servicio Odontológico')
                    ->placeholder('Ej: Limpieza dental, Ortodoncia, Endodoncia, etc.')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción del Procedimiento')
                    ->placeholder('Descripción detallada del servicio prestado')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('unit_price')
                    ->label('Precio del Servicio')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        // Usar el tax_rate configurado en InvoiceSettings
                        $taxRate = \App\Models\InvoiceSettings::get('tax_rate', 19);
                        $set('tax_percentage', $taxRate);
                    }),

                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad de Sesiones/Procedimientos')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->minValue(1)
                    ->step(1),

                Forms\Components\TextInput::make('tax_percentage')
                    ->label('IVA (%)')
                    ->numeric()
                    ->default(fn() => \App\Models\InvoiceSettings::get('tax_rate', 19))
                    ->suffix('%')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('El IVA se configura automáticamente según la configuración fiscal'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Servicio Odontológico')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción del Procedimiento')
                    ->limit(40)
                    ->tooltip(function ($record) {
                        return $record->description;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Sesiones')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio')
                    ->money('COP')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('tax_percentage')
                    ->label('IVA')
                    ->suffix('%')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->alignEnd()
                    ->state(function ($record) {
                        return $record->unit_price->multipliedBy($record->quantity);
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total con IVA')
                    ->money('COP')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success')
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
                    ->label('Agregar Servicio')
                    ->icon('heroicon-o-plus-circle'),
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
