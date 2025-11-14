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
                        // TAB 1: Información Personal del Cliente
                        Components\Tabs\Tab::make('Información Personal')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Components\Section::make('Datos Básicos')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\TextEntry::make('client.name')
                                                    ->label('Nombre Completo')
                                                    ->formatStateUsing(fn($record) => $record->client->name . ' ' . $record->client->apellido)
                                                    ->icon('heroicon-o-user')
                                                    ->badge()
                                                    ->color('primary'),
                                                
                                                Components\TextEntry::make('client.numero_documento')
                                                    ->label('Documento')
                                                    ->formatStateUsing(fn($record) => ($record->client->tipo_documento ?? 'CC') . ': ' . $record->client->numero_documento)
                                                    ->icon('heroicon-o-identification')
                                                    ->badge()
                                                    ->color('gray'),
                                                
                                                Components\TextEntry::make('client.genero')
                                                    ->label('Género')
                                                    ->icon('heroicon-o-user-group')
                                                    ->badge()
                                                    ->color(function ($state) {
                                                        return match ($state) {
                                                            'Masculino' => 'blue',
                                                            'Femenino' => 'pink',
                                                            default => 'gray',
                                                        };
                                                    }),
                                            ]),
                                        
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\TextEntry::make('client.fecha_nacimiento')
                                                    ->label('Fecha de Nacimiento')
                                                    ->formatStateUsing(fn($state) => $state ? $state->format('d/m/Y') : 'No disponible')
                                                    ->icon('heroicon-o-calendar-days')
                                                    ->badge()
                                                    ->color('info'),
                                                
                                                Components\TextEntry::make('client.fecha_nacimiento')
                                                    ->label('Edad')
                                                    ->formatStateUsing(fn($state) => $state ? $state->age . ' años' : 'No disponible')
                                                    ->icon('heroicon-o-clock')
                                                    ->badge()
                                                    ->color('success'),
                                                
                                                Components\TextEntry::make('client.aseguradora')
                                                    ->label('EPS/Aseguradora')
                                                    ->icon('heroicon-o-shield-check')
                                                    ->placeholder('No especificada')
                                                    ->badge()
                                                    ->color('blue'),
                                            ]),
                                        
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\TextEntry::make('client.ocupacion')
                                                    ->label('Ocupación')
                                                    ->icon('heroicon-o-briefcase')
                                                    ->placeholder('No especificada')
                                                    ->badge()
                                                    ->color('emerald'),
                                                
                                                Components\TextEntry::make('spacer')
                                                    ->label('')
                                                    ->state('')
                                                    ->hiddenLabel(),
                                            ]),
                                    ])
                                    ->columns(1),
                                
                                Components\Section::make('Información de Contacto')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\TextEntry::make('client.email')
                                                    ->label('Correo Electrónico')
                                                    ->icon('heroicon-o-envelope')
                                                    ->placeholder('No especificado')
                                                    ->badge()
                                                    ->color('success')
                                                    ->copyable(),
                                                
                                                Components\TextEntry::make('client.phone')
                                                    ->label('Teléfono')
                                                    ->icon('heroicon-o-phone')
                                                    ->placeholder('No especificado')
                                                    ->badge()
                                                    ->color('warning')
                                                    ->copyable(),
                                            ]),
                                        
                                        Components\TextEntry::make('client.address')
                                            ->label('Dirección')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('No especificada')
                                            ->badge()
                                            ->color('gray')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                
                                Components\Section::make('Contacto de Emergencia')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\TextEntry::make('client.nombre_contacto_emergencia')
                                                    ->label('Contacto de Emergencia')
                                                    ->icon('heroicon-o-user')
                                                    ->placeholder('No especificado')
                                                    ->badge()
                                                    ->color('orange'),
                                                
                                                Components\TextEntry::make('client.telefono_contacto_emergencia')
                                                    ->label('Teléfono de Emergencia')
                                                    ->icon('heroicon-o-phone')
                                                    ->placeholder('No especificado')
                                                    ->badge()
                                                    ->color('red')
                                                    ->copyable(),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        // TAB 2: Datos Generales de Salud
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

                                Components\Section::make('Información Médica Básica')
                                    ->schema([
                                        Components\TextEntry::make('tipo_sangre')
                                            ->label('Tipo de Sangre')
                                            ->badge()
                                            ->color(fn (?string $state): string => match ($state) {
                                                'O-' => 'danger',
                                                'O+' => 'warning', 
                                                'A+', 'B+' => 'info',
                                                'A-', 'B-' => 'primary',
                                                'AB+', 'AB-' => 'success',
                                                default => 'gray',
                                            })
                                            ->placeholder('No especificado'),
                                            
                                        Components\TextEntry::make('alergias')
                                            ->label('Alergias Conocidas')
                                            ->placeholder('Ninguna registrada')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                // Nueva sección de Anamnesis Básica
                                Components\Section::make('ANAMNESIS - Datos Básicos Sobre Salud')
                                    ->schema([
                                        // Datos generales de salud
                                        $this->createAnamnesisDisplay('tratamiento_medico', 'TRATAMIENTO MÉDICO'),
                                        $this->createAnamnesisDisplay('ingestion_medicamentos', 'INGESTIÓN DE MEDICAMENTOS'),
                                        $this->createAnamnesisDisplay('reaccion_alergica', 'REACCIÓN ALÉRGICA'),
                                        $this->createAnamnesisDisplay('hemorragias', 'HEMORRAGIAS'),
                                        $this->createAnamnesisDisplay('irradiaciones', 'IRRADIACIONES'),
                                        $this->createAnamnesisDisplay('sinusitis', 'SINUSITIS'),
                                        $this->createAnamnesisDisplay('enfermedades_respiratorias', 'ENFERMEDADES RESPIRATORIAS'),
                                        $this->createAnamnesisDisplay('cardiopatias', 'CARDIOPATÍAS'),
                                        $this->createAnamnesisDisplay('diabetes', 'DIABETES'),
                                        $this->createAnamnesisDisplay('fiebre_reumatica', 'FIEBRE REUMÁTICA'),

                                        // Segunda fila
                                        $this->createAnamnesisDisplay('hepatitis', 'HEPATITIS'),
                                        $this->createAnamnesisDisplay('hipertension', 'HIPERTENSIÓN'),
                                        
                                        // Campo de embarazo
                                        $this->createAnamnesisDisplay('embarazo', 'EMBARAZO'),

                                        // Subsección de Hábitos de Higiene Oral
                                        Components\Section::make('HÁBITOS DE HIGIENE ORAL')
                                            ->schema([
                                                Components\Grid::make(2)
                                                    ->schema([
                                                        $this->createAnamnesisDisplay('higiene_oral_cepillado', 'Cepillado'),
                                                        $this->createAnamnesisDisplay('higiene_oral_seda_dental', 'Seda Dental'),
                                                    ]),
                                            ])
                                            ->compact(),
                                            
                                        // Campo de texto para observaciones
                                        Components\TextEntry::make('anamnesis_basica.observaciones')
                                            ->label('OBSERVACIONES')
                                            ->columnSpanFull()
                                            ->state(function () {
                                                $anamnesisData = $this->record->anamnesis_basica ?? [];
                                                $value = $anamnesisData['observaciones'] ?? '';
                                                return !empty(trim($value)) ? $value : 'Sin observaciones';
                                            }),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                // Nueva sección de Examen Físico Estomatológico
                                Components\Section::make('EXAMEN FÍSICO ESTOMATOLÓGICO')
                                    ->schema([
                                        // Signos Vitales
                                        Components\Section::make('Signos vitales')
                                            ->schema([
                                                Components\Grid::make(4)
                                                    ->schema([
                                                        Components\TextEntry::make('examen_fisico_estomatologico.temperatura')
                                                            ->label('Temperatura')
                                                            ->suffix('°C')
                                                            ->state(function () {
                                                                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                                                                $value = $examenData['temperatura'] ?? '';
                                                                return !empty($value) ? $value : 'No registrado';
                                                            }),
                                                        
                                                        Components\TextEntry::make('examen_fisico_estomatologico.pulso')
                                                            ->label('Pulso')
                                                            ->suffix('lat/min')
                                                            ->state(function () {
                                                                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                                                                $value = $examenData['pulso'] ?? '';
                                                                return !empty($value) ? $value : 'No registrado';
                                                            }),
                                                        
                                                        Components\TextEntry::make('examen_fisico_estomatologico.presion_arterial')
                                                            ->label('Presión arterial')
                                                            ->suffix('mmHg')
                                                            ->state(function () {
                                                                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                                                                $value = $examenData['presion_arterial'] ?? '';
                                                                return !empty($value) ? $value : 'No registrado';
                                                            }),
                                                        
                                                        Components\TextEntry::make('examen_fisico_estomatologico.respiracion')
                                                            ->label('Respiración')
                                                            ->suffix('resp/min')
                                                            ->state(function () {
                                                                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                                                                $value = $examenData['respiracion'] ?? '';
                                                                return !empty($value) ? $value : 'No registrado';
                                                            }),
                                                    ]),
                                            ])
                                            ->compact(),
                                        
                                        // Exámenes por área anatómica
                                        Components\Section::make('Evaluación física')
                                            ->schema([
                                                Components\Grid::make(2)
                                                    ->schema([
                                                        $this->createExamenFisicoDisplay('art_temporomandibular', 'ART. TEMPOROMANDIBULAR'),
                                                        $this->createExamenFisicoDisplay('labios', 'LABIOS'),
                                                        $this->createExamenFisicoDisplay('lengua', 'LENGUA'),
                                                        $this->createExamenFisicoDisplay('paladar', 'PALADAR'),
                                                        $this->createExamenFisicoDisplay('piso_boca', 'PISO DE BOCA'),
                                                        $this->createExamenFisicoDisplay('carrillos', 'CARRILLOS'),
                                                        $this->createExamenFisicoDisplay('glandulas_salivales', 'GLÁNDULAS SALIVALES'),
                                                        $this->createExamenFisicoDisplay('maxilares', 'MAXILARES'),
                                                        $this->createExamenFisicoDisplay('senos_max', 'SENOS MAX.'),
                                                        $this->createExamenFisicoDisplay('musc_masticatorios', 'MUSC. MASTICATORIOS'),
                                                        $this->createExamenFisicoDisplay('sistema_nervioso_vascular_linfatico', 'SISTEMAS NERVIOSO VASCULAR LINFÁTICO REGIONAL'),
                                                        $this->createExamenFisicoDisplay('funcion_oclusion', 'FUNCIÓN DE OCLUSIÓN'),
                                                    ]),
                                            ])
                                            ->compact(),

                                        // Campo de observaciones
                                        Components\TextEntry::make('examen_fisico_estomatologico.observaciones')
                                            ->label('Observaciones')
                                            ->columnSpanFull()
                                            ->state(function () {
                                                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                                                $value = $examenData['observaciones'] ?? '';
                                                return !empty(trim($value)) ? $value : 'Sin observaciones';
                                            }),
                                    ])
                                    ->collapsible(),

                                // Nueva sección de Examen Dental
                                Components\Section::make('EXAMEN DENTAL')
                                    ->schema([
                                        // Hallazgos dentales principales
                                        Components\Section::make('Hallazgos dentales')
                                            ->schema([
                                                Components\Grid::make(2)
                                                    ->schema([
                                                        $this->createExamenDentalDisplay('supernumerarios', 'SUPERNUMERARIOS'),
                                                        $this->createExamenDentalDisplay('placa_blanda', 'PLACA BLANDA'),
                                                        $this->createExamenDentalDisplay('abrasion', 'ABRASIÓN'),
                                                        $this->createExamenDentalDisplay('placa_calcificada', 'PLACA CALCIFICADA'),
                                                        $this->createExamenDentalDisplay('manchas', 'MANCHAS'),
                                                        $this->createExamenDentalDisplay('patologia_pulpar', 'PATOLOGÍA PULPAR'),
                                                    ]),
                                                
                                                // Campo de texto para otros hallazgos
                                                Components\TextEntry::make('examen_dental.otros_cual')
                                                    ->label('OTROS')
                                                    ->columnSpanFull()
                                                    ->state(function () {
                                                        $examenData = $this->record->examen_dental ?? [];
                                                        $value = $examenData['otros_cual'] ?? '';
                                                        return !empty(trim($value)) ? $value : 'No especificado';
                                                    }),
                                            ])
                                            ->compact(),

                                        // Campo de observaciones
                                        Components\TextEntry::make('examen_dental.observaciones')
                                            ->label('OBSERVACIONES')
                                            ->columnSpanFull()
                                            ->state(function () {
                                                $examenData = $this->record->examen_dental ?? [];
                                                $value = $examenData['observaciones'] ?? '';
                                                return !empty(trim($value)) ? $value : 'Sin observaciones';
                                            }),

                                        // Sección de Estudios Recomendados
                                        Components\Section::make('Estudios recomendados')
                                            ->schema([
                                                Components\Grid::make(2)
                                                    ->schema([
                                                        $this->createExamenDentalDisplay('radiograficos', 'RADIOGRÁFICOS'),
                                                        $this->createExamenDentalDisplay('modelos_estudio', 'MODELOS DE ESTUDIO'),
                                                        $this->createExamenDentalDisplay('laboratorio_clinico', 'LABORATORIO CLÍNICO'),
                                                    ]),
                                                
                                                // Campo de texto para otros estudios
                                                Components\TextEntry::make('examen_dental.otros_estudios')
                                                    ->label('OTROS ESTUDIOS')
                                                    ->columnSpanFull()
                                                    ->state(function () {
                                                        $examenData = $this->record->examen_dental ?? [];
                                                        $value = $examenData['otros_estudios'] ?? '';
                                                        return !empty(trim($value)) ? $value : 'No especificado';
                                                    }),
                                            ])
                                            ->compact(),
                                    ])
                                    ->collapsible(),

                                // Nueva sección de Evaluación del Estado Periodontal
                                Components\Section::make('EVALUACIÓN DEL ESTADO PERIODONTAL')
                                    ->schema([
                                        // Evaluaciones periodontales
                                        Components\Section::make('Estado periodontal')
                                            ->schema([
                                                Components\Grid::make(2)
                                                    ->schema([
                                                        $this->createEvaluacionPeriodontalDisplay('placa_dentobacteriana', 'PLACA DENTOBACTERIANA'),
                                                        $this->createEvaluacionPeriodontalDisplay('periodontal', 'PERIODONTAL'),
                                                        $this->createEvaluacionPeriodontalDisplay('calculo_supragingival', 'CÁLCULO SUPRAGINGIVAL'),
                                                        $this->createEvaluacionPeriodontalDisplay('gingivitis', 'GINGIVITIS'),
                                                        $this->createEvaluacionPeriodontalDisplay('calculo_infragingival', 'CÁLCULO INFRAGINGIVAL'),
                                                        $this->createEvaluacionPeriodontalDisplay('sangrado_gingival', 'SANGRADO GINGIVAL'),
                                                        $this->createEvaluacionPeriodontalDisplay('movilidad', 'MOVILIDAD'),
                                                    ]),
                                                
                                                // Campo de texto para otros hallazgos
                                                Components\TextEntry::make('evaluacion_periodontal.otro_cual')
                                                    ->label('OTRO')
                                                    ->columnSpanFull()
                                                    ->state(function () {
                                                        $evaluacionData = $this->record->evaluacion_periodontal ?? [];
                                                        $value = $evaluacionData['otro_cual'] ?? '';
                                                        return !empty(trim($value)) ? $value : 'No especificado';
                                                    }),
                                            ])
                                            ->compact(),

                                        // Campo de observaciones
                                        Components\TextEntry::make('evaluacion_periodontal.observaciones')
                                            ->label('OBSERVACIONES')
                                            ->columnSpanFull()
                                            ->state(function () {
                                                $evaluacionData = $this->record->evaluacion_periodontal ?? [];
                                                $value = $evaluacionData['observaciones'] ?? '';
                                                return !empty(trim($value)) ? $value : 'Sin observaciones';
                                            }),
                                    ])
                                    ->collapsible(),

                                Components\Section::make('Historial Médico')
                                    ->schema([
                                        // Antecedentes médicos con badges organizados
                                        Components\Section::make('Antecedentes médicos')
                                            ->schema([
                                                Components\Grid::make(1)
                                                    ->schema([
                                                        $this->createHistorialMedicoDisplay('cirugias_previas', 'CIRUGÍAS PREVIAS'),
                                                        $this->createHistorialMedicoDisplay('hospitalizaciones', 'HOSPITALIZACIONES'),
                                                        $this->createHistorialMedicoDisplay('transfusiones', 'TRANSFUSIONES SANGUÍNEAS'),
                                                    ]),
                                            ])
                                            ->compact(),
                                        
                                        // Campo de texto para hábitos
                                        Components\TextEntry::make('habitos')
                                            ->label('HÁBITOS')
                                            ->columnSpanFull()
                                            ->placeholder('No registrado'),

                                        // Campo de observaciones
                                        Components\TextEntry::make('historial_observaciones')
                                            ->label('OBSERVACIONES')
                                            ->columnSpanFull()
                                            ->placeholder('Sin observaciones'),
                                    ])
                                    ->collapsible(),

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

    /**
     * Crear campo de visualización para anamnesis con formato de badge
     */
    private function createAnamnesisDisplay(string $key, string $label): Components\TextEntry
    {
        return Components\TextEntry::make("anamnesis_basica.{$key}")
            ->label($label)
            ->state(function () use ($key) {
                $anamnesisData = $this->record->anamnesis_basica ?? [];
                $value = $anamnesisData[$key] ?? null;

                return match($value) {
                    'si' => 'SÍ',
                    'no' => 'NO',
                    'no_sabe' => 'NO SABE',
                    default => 'No registrado'
                };
            })
            ->badge()
            ->icon(function () use ($key) {
                $anamnesisData = $this->record->anamnesis_basica ?? [];
                $value = $anamnesisData[$key] ?? null;

                return match($value) {
                    'si' => 'heroicon-o-check-circle',
                    'no' => 'heroicon-o-x-circle',
                    'no_sabe' => 'heroicon-o-question-mark-circle',
                    default => 'heroicon-o-minus-circle'
                };
            })
            ->color(function () use ($key) {
                $anamnesisData = $this->record->anamnesis_basica ?? [];
                $value = $anamnesisData[$key] ?? null;

                return match($value) {
                    'si' => 'success',
                    'no' => 'danger',
                    'no_sabe' => 'warning',
                    default => 'gray'
                };
            });
    }

    /**
     * Crear campo de visualización para examen físico con formato de badge
     */
    private function createExamenFisicoDisplay(string $key, string $label): Components\TextEntry
    {
        return Components\TextEntry::make("examen_fisico_estomatologico.{$key}")
            ->label($label)
            ->state(function () use ($key) {
                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                $value = $examenData[$key] ?? null;

                return match($value) {
                    'normal' => 'NORMAL',
                    'anormal' => 'ANORMAL',
                    default => 'No registrado'
                };
            })
            ->badge()
            ->icon(function () use ($key) {
                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                $value = $examenData[$key] ?? null;

                return match($value) {
                    'normal' => 'heroicon-o-check-circle',
                    'anormal' => 'heroicon-o-exclamation-triangle',
                    default => 'heroicon-o-minus-circle'
                };
            })
            ->color(function () use ($key) {
                $examenData = $this->record->examen_fisico_estomatologico ?? [];
                $value = $examenData[$key] ?? null;

                return match($value) {
                    'normal' => 'success',
                    'anormal' => 'warning',
                    default => 'gray'
                };
            });
    }

    /**
     * Crear campo de visualización para examen dental con formato de badge
     */
    private function createExamenDentalDisplay(string $key, string $label): Components\TextEntry
    {
        return Components\TextEntry::make("examen_dental.{$key}")
            ->label($label)
            ->state(function () use ($key) {
                $examenData = $this->record->examen_dental ?? [];
                $value = $examenData[$key] ?? null;

                return match($value) {
                    'si' => 'SÍ',
                    'no' => 'NO',
                    default => 'No registrado'
                };
            })
            ->badge()
            ->icon(function () use ($key) {
                $examenData = $this->record->examen_dental ?? [];
                $value = $examenData[$key] ?? null;

                return match($value) {
                    'si' => 'heroicon-o-exclamation-triangle',
                    'no' => 'heroicon-o-check-circle',
                    default => 'heroicon-o-minus-circle'
                };
            })
            ->color(function () use ($key) {
                $examenData = $this->record->examen_dental ?? [];
                $value = $examenData[$key] ?? null;

                return match($value) {
                    'si' => 'danger',  // Rojo para presencia de patología
                    'no' => 'success', // Verde para ausencia de patología (normal)
                    default => 'gray'
                };
            });
    }

    /**
     * Crear campo de visualización para historial médico con formato de badge
     */
    private function createHistorialMedicoDisplay(string $key, string $label): Components\TextEntry
    {
        return Components\TextEntry::make($key)
            ->label($label)
            ->state(function () use ($key) {
                $value = $this->record->{$key} ?? null;

                return match($value) {
                    'si' => 'SÍ',
                    'no' => 'NO',
                    default => 'No registrado'
                };
            })
            ->badge()
            ->icon(function () use ($key) {
                $value = $this->record->{$key} ?? null;

                return match($value) {
                    'si' => 'heroicon-o-exclamation-triangle',
                    'no' => 'heroicon-o-check-circle',
                    default => 'heroicon-o-minus-circle'
                };
            })
            ->color(function () use ($key) {
                $value = $this->record->{$key} ?? null;

                return match($value) {
                    'si' => 'warning',  // Amarillo para antecedentes positivos
                    'no' => 'success',  // Verde para antecedentes negativos (normal)
                    default => 'gray'
                };
            });
    }

    /**
     * Crear campo de visualización para evaluación periodontal con formato de badge
     */
    private function createEvaluacionPeriodontalDisplay(string $key, string $label): Components\TextEntry
    {
        return Components\TextEntry::make("evaluacion_periodontal.{$key}")
            ->label($label)
            ->state(function () use ($key) {
                $evaluacionData = $this->record->evaluacion_periodontal ?? [];
                $value = $evaluacionData[$key] ?? null;

                return match($value) {
                    'si' => 'SÍ',
                    'no' => 'NO',
                    default => 'No registrado'
                };
            })
            ->badge()
            ->icon(function () use ($key) {
                $evaluacionData = $this->record->evaluacion_periodontal ?? [];
                $value = $evaluacionData[$key] ?? null;

                return match($value) {
                    'si' => 'heroicon-o-exclamation-triangle',
                    'no' => 'heroicon-o-check-circle',
                    default => 'heroicon-o-minus-circle'
                };
            })
            ->color(function () use ($key) {
                $evaluacionData = $this->record->evaluacion_periodontal ?? [];
                $value = $evaluacionData[$key] ?? null;

                return match($value) {
                    'si' => 'danger',  // Rojo para presencia de problemas periodontales
                    'no' => 'success', // Verde para ausencia de problemas (normal)
                    default => 'gray'
                };
            });
    }
}
