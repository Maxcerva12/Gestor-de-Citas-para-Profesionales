<?php

namespace App\Filament\Resources\MedicalHistoryResource\Pages;

use App\Filament\Resources\MedicalHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewMedicalHistory extends ViewRecord
{
    protected static string $resource = MedicalHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Historia Clínica')
                ->icon('heroicon-o-pencil'),

            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->icon('heroicon-o-trash'),
        ];
    }

    public function getTitle(): string
    {
        return 'Historia Clínica de ' . $this->record->client->name . ' ' . $this->record->client->apellido;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Información del Paciente')
                    ->schema([
                        Components\TextEntry::make('client.name')
                            ->label('Paciente')
                            ->formatStateUsing(fn($record) => $record->client->name . ' ' . $record->client->apellido),
                        Components\TextEntry::make('client.numero_documento')
                            ->label('Número de Documento'),
                        Components\TextEntry::make('client.email')
                            ->label('Email'),
                        Components\TextEntry::make('client.phone')
                            ->label('Teléfono'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Components\Tabs::make('Información Clínica')
                    ->tabs([
                        // TAB 1: Datos Generales de Salud
                        Components\Tabs\Tab::make('Datos Generales de Salud')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Components\Section::make('Motivo de Consulta')
                                    ->schema([
                                        Components\TextEntry::make('motivo_consulta')
                                            ->label('Motivo de Consulta')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                        Components\TextEntry::make('enfermedad_actual')
                                            ->label('Enfermedad Actual')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                    ]),

                                Components\Section::make('Antecedentes Médicos')
                                    ->schema([
                                        Components\TextEntry::make('antecedentes_personales')
                                            ->label('Antecedentes Personales')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('antecedentes_familiares')
                                            ->label('Antecedentes Familiares')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('enfermedades_cronicas')
                                            ->label('Enfermedades Crónicas')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Components\Section::make('Medicamentos y Alergias')
                                    ->schema([
                                        Components\TextEntry::make('medicamentos_actuales')
                                            ->label('Medicamentos Actuales')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('alergias_medicamentos')
                                            ->label('Alergias a Medicamentos')
                                            ->placeholder('No registrado'),
                                    ])
                                    ->columns(2),

                                Components\Section::make('Historial Médico')
                                    ->schema([
                                        Components\TextEntry::make('cirugias_previas')
                                            ->label('Cirugías Previas')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('hospitalizaciones')
                                            ->label('Hospitalizaciones')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('transfusiones')
                                            ->label('Transfusiones Sanguíneas')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('habitos')
                                            ->label('Hábitos')
                                            ->placeholder('No registrado'),
                                    ])
                                    ->columns(2),

                                Components\Section::make('Antecedentes Odontológicos')
                                    ->schema([
                                        Components\TextEntry::make('ultima_visita_odontologo')
                                            ->label('Última Visita al Odontólogo')
                                            ->date('d/m/Y')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('higiene_oral_frecuencia')
                                            ->label('Frecuencia de Higiene Oral')
                                            ->placeholder('No registrado'),
                                        Components\TextEntry::make('motivo_ultima_visita')
                                            ->label('Motivo de Última Visita')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                        Components\TextEntry::make('tratamientos_previos')
                                            ->label('Tratamientos Odontológicos Previos')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                        Components\IconEntry::make('sangrado_encias')
                                            ->label('Sangrado de Encías')
                                            ->boolean(),
                                        Components\IconEntry::make('sensibilidad_dental')
                                            ->label('Sensibilidad Dental')
                                            ->boolean(),
                                        Components\IconEntry::make('bruxismo')
                                            ->label('Bruxismo')
                                            ->boolean(),
                                        Components\IconEntry::make('ortodoncia_previa')
                                            ->label('Ortodoncia Previa')
                                            ->boolean(),
                                    ])
                                    ->columns(2),
                            ]),

                        // TAB 2: Odontograma (Readonly)
                        Components\Tabs\Tab::make('Odontograma')
                            ->icon('heroicon-o-finger-print')
                            ->schema([
                                Components\Section::make('Odontograma Dental')
                                    ->description('Mapa visual del estado actual de todas las piezas dentales del paciente')
                                    ->schema([
                                        Components\View::make('infolists.components.odontogram-readonly')
                                            ->columnSpanFull(),
                                        Components\TextEntry::make('odontogram_observations')
                                            ->label('Observaciones del Odontograma')
                                            ->placeholder('Sin observaciones registradas')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 3: Diagnóstico y Tratamiento
                        Components\Tabs\Tab::make('Diagnóstico y Tratamiento')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Components\Section::make('Diagnóstico')
                                    ->schema([
                                        Components\TextEntry::make('diagnostico_principal')
                                            ->label('Diagnóstico Principal')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                        Components\TextEntry::make('pronostico')
                                            ->label('Pronóstico')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                    ]),

                                Components\Section::make('Plan de Tratamiento')
                                    ->schema([
                                        Components\TextEntry::make('plan_tratamiento')
                                            ->label('Plan de Tratamiento')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                    ]),

                                Components\Section::make('Observaciones Generales')
                                    ->schema([
                                        Components\TextEntry::make('observaciones_generales')
                                            ->label('Observaciones Generales')
                                            ->placeholder('No registrado')
                                            ->columnSpanFull(),
                                        Components\IconEntry::make('consentimiento_informado')
                                            ->label('Consentimiento Informado Firmado')
                                            ->boolean(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Components\Section::make('Información del Registro')
                    ->schema([
                        Components\TextEntry::make('creator.name')
                            ->label('Creado por')
                            ->placeholder('Sistema'),
                        Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i'),
                        Components\TextEntry::make('updater.name')
                            ->label('Última actualización por')
                            ->placeholder('Sistema'),
                        Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
