<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalHistoryResource\Pages;
use App\Filament\Resources\MedicalHistoryResource\RelationManagers;
use App\Models\MedicalHistory;
use App\Models\Client;
use App\Forms\Components\Odontogram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MedicalHistoryResource extends Resource
{
    protected static ?string $model = MedicalHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Historias Clínicas';
    protected static ?string $navigationGroup = 'Gestión Clínica';
    protected static ?string $label = 'Historia Clínica';
    protected static ?string $pluralLabel = 'Historias Clínicas';
    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Gate::allows('view_any_medical::history');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_medical::history', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_medical::history');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_medical::history', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_medical::history', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_medical::history');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('view_any_medical::history');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['client.name', 'client.apellido', 'client.numero_documento', 'diagnostico_principal'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->client->name . ' ' . $record->client->apellido;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Paciente')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Paciente')
                            ->relationship('client', 'name')
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} {$record->apellido} - {$record->numero_documento}")
                            ->searchable(['name', 'apellido', 'numero_documento'])
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('apellido')
                                    ->label('Apellido')
                                    ->required(),
                                Forms\Components\TextInput::make('numero_documento')
                                    ->label('Número de Documento')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Tabs::make('Información Clínica')
                    ->tabs([
                        // TAB 1: Datos Generales de Salud
                        Forms\Components\Tabs\Tab::make('Datos Generales de Salud')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\Section::make('Motivo de Consulta')
                                    ->schema([
                                        Forms\Components\Textarea::make('motivo_consulta')
                                            ->label('Motivo de Consulta Actual')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('enfermedad_actual')
                                            ->label('Enfermedad Actual')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                // Nueva sección de Anamnesis Básica
                                Forms\Components\Section::make('ANAMNESIS - Datos Básicos Sobre Salud')
                                    ->description('Seleccione SÍ, NO o NO SABE para cada pregunta')
                                    ->schema([
                                        // Preguntas organizadas en 2 columnas para mejor legibilidad
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('anamnesis_basica.tratamiento_medico')
                                                    ->label('TRATAMIENTO MÉDICO')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('anamnesis_basica.ingestion_medicamentos')
                                                    ->label('INGESTIÓN DE MEDICAMENTOS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('anamnesis_basica.reaccion_alergica')
                                                    ->label('REACCIÓN ALÉRGICA')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('anamnesis_basica.hemorragias')
                                                    ->label('HEMORRAGIAS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('anamnesis_basica.irradiaciones')
                                                    ->label('IRRADIACIONES')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('anamnesis_basica.sinusitis')
                                                    ->label('SINUSITIS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('anamnesis_basica.enfermedades_respiratorias')
                                                    ->label('ENFERMEDADES RESPIRATORIAS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('anamnesis_basica.cardiopatias')
                                                    ->label('CARDIOPATÍAS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('anamnesis_basica.diabetes')
                                                    ->label('DIABETES')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('anamnesis_basica.fiebre_reumatica')
                                                    ->label('FIEBRE REUMÁTICA')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('anamnesis_basica.hepatitis')
                                                    ->label('HEPATITIS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('anamnesis_basica.hipertension')
                                                    ->label('HIPERTENSIÓN')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        // Sección de Hábitos de Higiene Oral
                                        Forms\Components\Section::make('HÁBITOS DE HIGIENE ORAL')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Radio::make('anamnesis_basica.higiene_oral_cepillado')
                                                            ->label('Cepillado')
                                                            ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                            ->inline()
                                                            ->columnSpan(1),
                                                        
                                                        Forms\Components\Radio::make('anamnesis_basica.higiene_oral_seda_dental')
                                                            ->label('Seda Dental')
                                                            ->options(['si' => 'SÍ', 'no' => 'NO', 'no_sabe' => 'NO SABE'])
                                                            ->inline()
                                                            ->columnSpan(1),
                                                    ]),
                                            ])
                                            ->compact(),

                                        Forms\Components\Textarea::make('anamnesis_basica.observaciones')
                                            ->label('OBSERVACIONES:')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                // Nueva sección de Examen Físico Estomatológico
                                Forms\Components\Section::make('EXAMEN FÍSICO ESTOMATOLÓGICO')
                                    ->description('Evaluación física del paciente')
                                    ->schema([
                                        // Signos Vitales
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\TextInput::make('examen_fisico_estomatologico.temperatura')
                                                    ->label('Temperatura (°C)')
                                                    ->numeric()
                                                    ->placeholder('36.5')
                                                    ->suffix('°C')
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\TextInput::make('examen_fisico_estomatologico.pulso')
                                                    ->label('Pulso (lat/min)')
                                                    ->numeric()
                                                    ->placeholder('72')
                                                    ->suffix('lat/min')
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\TextInput::make('examen_fisico_estomatologico.presion_arterial')
                                                    ->label('Presión arterial (mmHg)')
                                                    ->placeholder('120/80')
                                                    ->suffix('mmHg')
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\TextInput::make('examen_fisico_estomatologico.respiracion')
                                                    ->label('Respiración (resp/min)')
                                                    ->numeric()
                                                    ->placeholder('16')
                                                    ->suffix('resp/min')
                                                    ->columnSpan(1),
                                            ]),
                                        
                                        // Examenes por área anatómica
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.art_temporomandibular')
                                                    ->label('ART. TEMPOROMANDIBULAR')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.labios')
                                                    ->label('LABIOS')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.lengua')
                                                    ->label('LENGUA')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.paladar')
                                                    ->label('PALADAR')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.piso_boca')
                                                    ->label('PISO DE BOCA')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.carrillos')
                                                    ->label('CARRILLOS')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.glandulas_salivales')
                                                    ->label('GLÁNDULAS SALIVALES')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.maxilares')
                                                    ->label('MAXILARES')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.senos_max')
                                                    ->label('SENOS MAX.')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.musc_masticatorios')
                                                    ->label('MUSC. MASTICATORIOS')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.sistema_nervioso_vascular_linfatico')
                                                    ->label('SISTEMAS NERVIOSO VASCULAR LINFÁTICO REGIONAL')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_fisico_estomatologico.funcion_oclusion')
                                                    ->label('FUNCIÓN DE OCLUSIÓN')
                                                    ->options(['normal' => 'NORMAL', 'anormal' => 'ANORMAL'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Textarea::make('examen_fisico_estomatologico.observaciones')
                                            ->label('Observaciones:')
                                            ->placeholder('Describa cualquier hallazgo anormal o relevante...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                // Nueva sección de Examen Dental
                                Forms\Components\Section::make('EXAMEN DENTAL')
                                    ->description('Evaluación del estado dental del paciente')
                                    ->schema([
                                        // Primera subsección de hallazgos dentales
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_dental.supernumerarios')
                                                    ->label('SUPERNUMERARIOS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_dental.placa_blanda')
                                                    ->label('PLACA BLANDA')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_dental.abrasion')
                                                    ->label('ABRASIÓN')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('examen_dental.placa_calcificada')
                                                    ->label('PLACA CALCIFICADA')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_dental.manchas')
                                                    ->label('MANCHAS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\TextInput::make('examen_dental.otros_cual')
                                                    ->label('OTROS (CUÁL)')
                                                    ->placeholder('Especificar otros hallazgos...')
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Radio::make('examen_dental.patologia_pulpar')
                                                    ->label('PATOLOGÍA PULPAR')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        // Campo de observaciones para el examen dental
                                        Forms\Components\Textarea::make('examen_dental.observaciones')
                                            ->label('OBSERVACIONES:')
                                            ->placeholder('Describa hallazgos específicos del examen dental...')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        // Sección de Estudios Recomendados
                                        Forms\Components\Section::make('ESTUDIOS RECOMENDADOS')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Radio::make('examen_dental.radiograficos')
                                                            ->label('RADIOGRÁFICOS')
                                                            ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                            ->inline()
                                                            ->columnSpan(1),
                                                        
                                                        Forms\Components\Radio::make('examen_dental.modelos_estudio')
                                                            ->label('MODELOS DE ESTUDIO')
                                                            ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                            ->inline()
                                                            ->columnSpan(1),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Radio::make('examen_dental.laboratorio_clinico')
                                                            ->label('LABORATORIO CLÍNICO')
                                                            ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                            ->inline()
                                                            ->columnSpan(1),
                                                        
                                                        Forms\Components\TextInput::make('examen_dental.otros_estudios')
                                                            ->label('OTROS')
                                                            ->placeholder('Especificar otros estudios...')
                                                            ->columnSpan(1),
                                                    ]),
                                            ])
                                            ->compact(),
                                    ])
                                    ->collapsible(),

                                // Nueva sección de Evaluación del Estado Periodontal
                                Forms\Components\Section::make('EVALUACIÓN DEL ESTADO PERIODONTAL')
                                    ->description('Evaluación del estado de las encías y estructuras de soporte')
                                    ->schema([
                                        // Primera fila de evaluaciones periodontales
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('evaluacion_periodontal.placa_dentobacteriana')
                                                    ->label('PLACA DENTOBACTERIANA')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('evaluacion_periodontal.periodontal')
                                                    ->label('PERIODONTAL')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('evaluacion_periodontal.calculo_supragingival')
                                                    ->label('CÁLCULO SUPRAGINGIVAL')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('evaluacion_periodontal.gingivitis')
                                                    ->label('GINGIVITIS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('evaluacion_periodontal.calculo_infragingival')
                                                    ->label('CÁLCULO INFRAGINGIVAL')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Radio::make('evaluacion_periodontal.sangrado_gingival')
                                                    ->label('SANGRADO GINGIVAL')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Radio::make('evaluacion_periodontal.movilidad')
                                                    ->label('MOVILIDAD')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\TextInput::make('evaluacion_periodontal.otro_cual')
                                                    ->label('OTRO')
                                                    ->placeholder('Especificar otros hallazgos periodontales...')
                                                    ->columnSpan(1),
                                            ]),

                                        // Campo de observaciones para la evaluación periodontal
                                        Forms\Components\Textarea::make('evaluacion_periodontal.observaciones')
                                            ->label('OBSERVACIONES:')
                                            ->placeholder('Describa hallazgos específicos de la evaluación periodontal...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Historial Médico')
                                    ->schema([
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Radio::make('cirugias_previas')
                                                    ->label('CIRUGÍAS PREVIAS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),

                                                Forms\Components\Radio::make('hospitalizaciones')
                                                    ->label('HOSPITALIZACIONES')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),

                                                Forms\Components\Radio::make('transfusiones')
                                                    ->label('TRANSFUSIONES SANGUÍNEAS')
                                                    ->options(['si' => 'SÍ', 'no' => 'NO'])
                                                    ->inline()
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Textarea::make('habitos')
                                            ->label('HÁBITOS:')
                                            ->helperText('Tabaco, alcohol, drogas, ejercicio, etc.')
                                            ->placeholder('Describa los hábitos del paciente...')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('historial_observaciones')
                                            ->label('OBSERVACIONES:')
                                            ->placeholder('Observaciones adicionales sobre el historial médico...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->collapsible(),

                                Forms\Components\Section::make('Antecedentes Odontológicos')
                                    ->schema([
                                        Forms\Components\DatePicker::make('ultima_visita_odontologo')
                                            ->label('Última Visita al Odontólogo')
                                            ->maxDate(now()),

                                        Forms\Components\TextInput::make('higiene_oral_frecuencia')
                                            ->label('Frecuencia de Higiene Oral')
                                            ->placeholder('Ej: 3 veces al día'),

                                        Forms\Components\Textarea::make('motivo_ultima_visita')
                                            ->label('Motivo de Última Visita')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('tratamientos_previos')
                                            ->label('Tratamientos Odontológicos Previos')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('experiencias_traumaticas')
                                            ->label('Experiencias Traumáticas en Tratamientos Dentales')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('sangrado_encias')
                                            ->label('Sangrado de Encías')
                                            ->inline(false),

                                        Forms\Components\Toggle::make('sensibilidad_dental')
                                            ->label('Sensibilidad Dental')
                                            ->inline(false),

                                        Forms\Components\Toggle::make('bruxismo')
                                            ->label('Bruxismo (Rechina los dientes)')
                                            ->inline(false),

                                        Forms\Components\Toggle::make('ortodoncia_previa')
                                            ->label('Ortodoncia Previa')
                                            ->inline(false),
                                    ])
                                    ->columns(2),
                            ]),

                        // TAB 2: Odontograma
                        Forms\Components\Tabs\Tab::make('Odontograma')
                            ->icon('heroicon-o-finger-print')
                            ->schema([
                                Forms\Components\Section::make('Odontograma Dental')
                                    ->description('Mapa visual del estado actual de todas las piezas dentales del paciente')
                                    ->schema([
                                        Odontogram::make('odontogram')
                                            ->label('')
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('odontogram_observations')
                                            ->label('Observaciones del Odontograma')
                                            ->helperText('Notas adicionales sobre el estado dental del paciente')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),

                        // TAB 3: Diagnóstico y Tratamiento
                        Forms\Components\Tabs\Tab::make('Diagnóstico y Tratamiento')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Forms\Components\Section::make('Diagnóstico')
                                    ->schema([
                                        Forms\Components\Textarea::make('diagnostico_principal')
                                            ->label('Diagnóstico Principal')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('pronostico')
                                            ->label('Pronóstico')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Plan de Tratamiento')
                                    ->schema([
                                        Forms\Components\Textarea::make('plan_tratamiento')
                                            ->label('Plan de Tratamiento')
                                            ->helperText('Descripción detallada del tratamiento propuesto')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Observaciones Generales')
                                    ->schema([
                                        Forms\Components\Textarea::make('observaciones_generales')
                                            ->label('Observaciones Generales')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('consentimiento_informado')
                                            ->label('Consentimiento Informado Firmado')
                                            ->helperText('Indica si el paciente ha firmado el consentimiento informado')
                                            ->inline(false),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Paciente')
                    ->description(fn(MedicalHistory $record): string => $record->client->numero_documento ?? '')
                    ->formatStateUsing(fn(MedicalHistory $record) => $record->client->name . ' ' . $record->client->apellido)
                    ->searchable(['name', 'apellido'])
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('diagnostico_principal')
                    ->label('Diagnóstico')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_odontogram')
                    ->label('Odontograma')
                    ->boolean()
                    ->getStateUsing(fn(MedicalHistory $record): bool => $record->has_odontogram)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn(MedicalHistory $record) => $record->has_odontogram ? 'Tiene odontograma registrado' : 'Sin odontograma')
                    // ->tooltip(fn($record) => $record->has_odontogram ? 'Tiene odontograma registrado' : 'Sin odontograma')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('consentimiento_informado')
                    ->label('Consentimiento')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->tooltip(fn($record) => $record->consentimiento_informado ? 'Tiene consentimiento informado' : 'Sin consentimiento informado')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('evolution_notes_count')
                    ->label('Notas de Evolución')
                    ->counts('evolutionNotes')
                    ->badge()
                    ->color('info')
                    ->toggleable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('clinical_documents_count')
                    ->label('Documentos')
                    ->counts('clinicalDocuments')
                    ->badge()
                    ->color('warning')
                    ->toggleable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Creado por')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Paciente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('has_odontogram')
                    ->label('Tiene Odontograma')
                    ->native(false)
                    ->placeholder('Todos')
                    ->trueLabel('Con Odontograma')
                    ->falseLabel('Sin Odontograma')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('odontogram'),
                        false: fn(Builder $query) => $query->whereNull('odontogram'),
                    ),

                Tables\Filters\TernaryFilter::make('consentimiento_informado')
                    ->label('Consentimiento Firmado')
                    ->native(false)
                    ->placeholder('Todos')
                    ->trueLabel('Con Consentimiento')
                    ->falseLabel('Sin Consentimiento'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\EvolutionNotesRelationManager::class,
            RelationManagers\ClinicalDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicalHistories::route('/'),
            'create' => Pages\CreateMedicalHistory::route('/create'),
            'view' => Pages\ViewMedicalHistory::route('/{record}'),
            'edit' => Pages\EditMedicalHistory::route('/{record}/edit'),
        ];
    }
}
