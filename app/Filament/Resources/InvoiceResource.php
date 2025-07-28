<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceState;
use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Client;
use App\Models\Invoice;
use Brick\Money\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Facturas';

    protected static ?string $modelLabel = 'Factura';

    protected static ?string $pluralModelLabel = 'Facturas';

    protected static ?string $navigationGroup = 'Facturación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Documento')
                            ->options(InvoiceType::class)
                            ->default('invoice')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('state')
                            ->label('Estado')
                            ->options(InvoiceState::class)
                            ->default('draft')
                            ->required(),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('Número de Serie')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('due_at')
                            ->label('Fecha de Vencimiento')
                            ->default(now()->addDays(30))
                            ->required(),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Fecha de Pago')
                            ->visible(fn($record) => $record?->state === InvoiceState::Paid),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Cliente')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono'),
                                Forms\Components\TextInput::make('document_number')
                                    ->label('Número de Documento'),
                                Forms\Components\Textarea::make('address')
                                    ->label('Dirección'),
                            ])
                            ->required(),

                        Forms\Components\KeyValue::make('buyer_information.fields')
                            ->label('Información Adicional del Cliente')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor'),
                    ]),

                Forms\Components\Section::make('Información del Vendedor')
                    ->schema([
                        Forms\Components\KeyValue::make('seller_information.fields')
                            ->label('Campos Personalizados')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->default([
                                'Régimen' => 'Común',
                                'Actividad Económica' => 'Servicios Profesionales',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(InvoiceType $state): string => $state->getColor()),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(InvoiceState $state): string => $state->getColor()),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(InvoiceType::class),

                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options(InvoiceState::class),

                Tables\Filters\Filter::make('overdue')
                    ->label('Vencidas')
                    ->query(fn(Builder $query): Builder => $query->where('due_at', '<', now())->where('state', '!=', 'paid')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('download_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Marcar como Pagada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Invoice $record) {
                        $record->update([
                            'state' => InvoiceState::Paid,
                            'paid_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(Invoice $record): bool => $record->state !== InvoiceState::Paid),

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
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('state', InvoiceState::Draft)->count();
    }
}
