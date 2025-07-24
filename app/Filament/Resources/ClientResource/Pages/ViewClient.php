<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use App\Forms\Components\Odontogram;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Tabs::make('Tabs')
                    ->tabs([
                        Components\Tabs\Tab::make('Información Personal')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Components\Section::make('Datos Básicos')
                                    ->description('Información principal del cliente')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\Group::make([
                                                    Components\TextEntry::make('name')
                                                        ->label('Nombre')
                                                        ->weight('bold')
                                                        ->size('lg'),
                                                    Components\TextEntry::make('email')
                                                        ->label('Correo Electrónico')
                                                        ->icon('heroicon-m-envelope')
                                                        ->copyable(),
                                                    Components\TextEntry::make('phone')
                                                        ->label('Teléfono')
                                                        ->icon('heroicon-m-phone')
                                                        ->copyable(),
                                                ])
                                                    ->columnSpan(2),
                                                Components\ImageEntry::make('avatar_url')
                                                    ->label('Avatar')
                                                    ->circular()
                                                    ->defaultImageUrl(fn($record): string => "https://ui-avatars.com/api/?name=" . urlencode($record->name) . "&color=FFFFFF&background=3B82F6")
                                                    ->size(100)
                                                    ->columnSpan(1),
                                            ]),
                                    ]),
                                Components\Section::make('Ubicación')
                                    ->description('Datos de dirección del cliente')
                                    ->schema([
                                        Components\TextEntry::make('address')
                                            ->label('Dirección')
                                            ->placeholder('No especificada'),
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\TextEntry::make('city')
                                                    ->label('Ciudad')
                                                    ->placeholder('No especificada'),
                                                Components\TextEntry::make('country')
                                                    ->label('País')
                                                    ->placeholder('No especificado'),
                                            ]),
                                    ]),
                            ]),
                        Components\Tabs\Tab::make('Cuenta')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Components\Section::make('Estado de la Cuenta')
                                    ->description('Información del estado del cliente')
                                    ->schema([
                                        Components\IconEntry::make('active')
                                            ->label('Estado')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-check-circle')
                                            ->falseIcon('heroicon-o-x-circle')
                                            ->trueColor('success')
                                            ->falseColor('danger'),
                                        Components\TextEntry::make('created_at')
                                            ->label('Fecha de Registro')
                                            ->dateTime('d/m/Y H:i'),
                                        Components\TextEntry::make('updated_at')
                                            ->label('Última Actualización')
                                            ->dateTime('d/m/Y H:i'),
                                    ]),
                                Components\Section::make('Información Adicional')
                                    ->description('Datos adicionales del cliente')
                                    ->schema([
                                        Components\KeyValueEntry::make('custom_fields')
                                            ->label('Campos Personalizados')
                                            ->placeholder('No hay campos personalizados'),
                                    ]),
                            ]),
                        Components\Tabs\Tab::make('Notas')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Components\Section::make()
                                    ->schema([
                                        Components\ViewEntry::make('notes')
                                            ->label('Notas Internas')
                                            ->view('infolists.components.rich-text-display')
                                            ->state(fn($record) => $record->notes),
                                    ]),
                            ]),
                        Components\Tabs\Tab::make('Odontograma')
                            ->icon('heroicon-o-face-smile')
                            ->schema([
                                Components\Section::make('Información Dental')
                                    ->description('Datos odontológicos del paciente')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\TextEntry::make('last_dental_visit')
                                                    ->label('Última Visita Dental')
                                                    ->date('d/m/Y')
                                                    ->placeholder('No registrada'),
                                                Components\TextEntry::make('dental_notes')
                                                    ->label('Notas Dentales')
                                                    ->placeholder('Sin observaciones registradas'),
                                            ]),
                                    ]),
                                Components\Section::make('Odontograma Interactivo')
                                    ->description('Estado actual de los dientes del paciente')
                                    ->schema([
                                        Components\ViewEntry::make('odontogram_display')
                                            ->label('')
                                            ->view('infolists.components.odontogram-readonly')
                                            ->state(fn($record) => $record),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
