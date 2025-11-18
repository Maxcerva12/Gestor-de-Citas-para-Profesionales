<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Paciente'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Podríamos agregar widgets aquí en el futuro
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
                                    ->description('Información principal del paciente')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\Group::make([
                                                    Components\TextEntry::make('name')
                                                        ->label('Nombre')
                                                        ->weight('bold')
                                                        ->size('lg')
                                                        ->formatStateUsing(fn($record) => $record->name . ' ' . ($record->apellido ?? '')),
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
                                                    ->defaultImageUrl(fn($record): string => "https://ui-avatars.com/api/?name=" . urlencode($record->name . ' ' . ($record->apellido ?? '')) . "&color=FFFFFF&background=3B82F6")
                                                    ->size(120)
                                                    ->columnSpan(1),
                                            ]),
                                    ]),

                                Components\Section::make('Documentación')
                                    ->description('Información de identificación del paciente')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\TextEntry::make('tipo_documento')
                                                    ->label('Tipo de Documento')
                                                    ->formatStateUsing(fn($state) => match ($state) {
                                                        'CC' => 'Cédula de Ciudadanía',
                                                        'CE' => 'Cédula de Extranjería',
                                                        'TI' => 'Tarjeta de Identidad',
                                                        'PP' => 'Pasaporte',
                                                        default => $state ?? 'No especificado'
                                                    })
                                                    ->badge()
                                                    ->color('primary'),
                                                Components\TextEntry::make('numero_documento')
                                                    ->label('Número de Documento')
                                                    ->copyable()
                                                    ->icon('heroicon-m-identification')
                                                    ->placeholder('No especificado'),
                                                Components\TextEntry::make('genero')
                                                    ->label('Género')
                                                    ->badge()
                                                    ->color(fn($state) => match ($state) {
                                                        'Masculino' => 'blue',
                                                        'Femenino' => 'pink',
                                                        'Otro' => 'gray',
                                                        default => 'gray'
                                                    })
                                                    ->placeholder('No especificado'),
                                            ]),
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\TextEntry::make('fecha_nacimiento')
                                                    ->label('Fecha de Nacimiento')
                                                    ->date('d/m/Y')
                                                    ->icon('heroicon-m-calendar-days')
                                                    ->badge()
                                                    ->color('info')
                                                    ->placeholder('No especificada'),
                                                Components\TextEntry::make('fecha_nacimiento')
                                                    ->label('Edad')
                                                    ->formatStateUsing(fn($state) => $state ? $state->age . ' años' : 'No disponible')
                                                    ->icon('heroicon-m-clock')
                                                    ->badge()
                                                    ->color('success'),
                                                Components\TextEntry::make('ocupacion')
                                                    ->label('Ocupación')
                                                    ->icon('heroicon-m-briefcase')
                                                    ->badge()
                                                    ->color('emerald')
                                                    ->placeholder('No especificada'),
                                            ]),
                                        Components\Grid::make(1)
                                            ->schema([
                                                Components\TextEntry::make('aseguradora')
                                                    ->label('EPS/Aseguradora')
                                                    ->icon('heroicon-m-shield-check')
                                                    ->badge()
                                                    ->color('blue')
                                                    ->placeholder('No especificada'),
                                            ]),
                                    ]),

                                Components\Section::make('Contacto de Emergencia')
                                    ->description('Persona a contactar en caso de emergencia')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\TextEntry::make('nombre_contacto_emergencia')
                                                    ->label('Nombre de Contacto de Emergencia')
                                                    ->icon('heroicon-m-user')
                                                    ->placeholder('No especificado'),
                                                Components\TextEntry::make('telefono_contacto_emergencia')
                                                    ->label('Teléfono de Contacto de Emergencia')
                                                    ->icon('heroicon-m-phone')
                                                    ->copyable()
                                                    ->placeholder('No especificado'),
                                            ]),
                                    ]),

                                Components\Section::make('Ubicación')
                                    ->description('Datos de dirección del paciente')
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
                                    ->description('Información del estado del paciente en el sistema')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\IconEntry::make('active')
                                                    ->label('Estado de la Cuenta')
                                                    ->boolean()
                                                    ->trueIcon('heroicon-o-check-circle')
                                                    ->falseIcon('heroicon-o-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger'),
                                                Components\TextEntry::make('created_at')
                                                    ->label('Fecha de Registro')
                                                    ->dateTime('d/m/Y H:i')
                                                    ->icon('heroicon-m-calendar'),
                                                Components\TextEntry::make('updated_at')
                                                    ->label('Última Actualización')
                                                    ->dateTime('d/m/Y H:i')
                                                    ->icon('heroicon-m-clock'),
                                            ]),
                                    ]),
                                Components\Section::make('Información Adicional')
                                    ->description('Datos adicionales del paciente')
                                    ->schema([
                                        Components\KeyValueEntry::make('custom_fields')
                                            ->label('Campos Personalizados')
                                            ->placeholder('No hay campos personalizados registrados'),
                                    ]),
                            ]),
                        Components\Tabs\Tab::make('Tratamiento de Datos')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Components\Section::make('Autorización para el Tratamiento de Datos Personales')
                                    ->description('Estado del consentimiento del paciente para el tratamiento de datos')
                                    ->schema([
                                        Components\ViewEntry::make('data_treatment_legal_text')
                                            ->label('Información Legal')
                                            ->view('infolists.components.rich-text-display')
                                            ->state(function () {
                                                return 'De conformidad con la Ley 1581 de 2012 y el Decreto 1377 de 2013, la Fundación Odontológica Zoila Padilla requiere su autorización previa, expresa e informada para realizar el tratamiento de sus datos personales.

Sus datos serán utilizados para la gestión y programación de citas médicas, creación y mantenimiento de su historia clínica, procesos de facturación y gestión administrativa. También podremos comunicarnos con usted sobre servicios, recordatorios de citas y promociones, siempre cumpliendo con las obligaciones legales del sector salud y mejorando continuamente nuestros servicios profesionales.

Como titular de sus datos personales, usted tiene derecho a conocer, actualizar, rectificar y solicitar la supresión de sus datos, así como revocar la autorización otorgada para su tratamiento. Para ejercer estos derechos, puede contactarnos a través de nuestros canales de atención establecidos.';
                                            }),
                                        
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\IconEntry::make('accepts_data_treatment')
                                                    ->label('Autorización de Tratamiento')
                                                    ->boolean()
                                                    ->trueIcon('heroicon-o-check-circle')
                                                    ->falseIcon('heroicon-o-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger'),
                                                Components\IconEntry::make('accepts_privacy_policy')
                                                    ->label('Acepta Políticas de Privacidad')
                                                    ->boolean()
                                                    ->trueIcon('heroicon-o-check-circle')
                                                    ->falseIcon('heroicon-o-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger'),
                                                Components\TextEntry::make('data_treatment_date')
                                                    ->label('Fecha de Aceptación')
                                                    ->dateTime('d/m/Y H:i')
                                                    ->placeholder('No registrada')
                                                    ->icon('heroicon-o-calendar-days')
                                                    ->badge()
                                                    ->color('success')
                                                    ->weight('bold'),
                                            ]),
                                    ]),
                            ]),
                        Components\Tabs\Tab::make('Notas Médicas')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Components\Section::make('Notas Internas')
                                    ->description('Observaciones del personal médico (solo visible para administradores)')
                                    ->schema([
                                        Components\ViewEntry::make('notes')
                                            ->label('Notas Internas')
                                            ->view('infolists.components.rich-text-display')
                                            ->state(fn($record) => $record->notes)
                                            ->placeholder('Sin notas registradas'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
