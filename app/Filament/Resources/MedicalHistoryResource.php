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

                                Forms\Components\Section::make('Antecedentes Médicos')
                                    ->schema([
                                        Forms\Components\Textarea::make('antecedentes_personales')
                                            ->label('Antecedentes Personales')
                                            ->helperText('Enfermedades previas, condiciones crónicas, etc.')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('antecedentes_familiares')
                                            ->label('Antecedentes Familiares')
                                            ->helperText('Enfermedades hereditarias en la familia')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('enfermedades_cronicas')
                                            ->label('Enfermedades Crónicas')
                                            ->helperText('Diabetes, hipertensión, cardiopatías, etc.')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Medicamentos y Alergias')
                                    ->schema([
                                        Forms\Components\Textarea::make('medicamentos_actuales')
                                            ->label('Medicamentos Actuales')
                                            ->helperText('Lista de medicamentos que está tomando actualmente')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('alergias_medicamentos')
                                            ->label('Alergias a Medicamentos')
                                            ->helperText('Especificar cualquier alergia conocida')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Historial Médico')
                                    ->schema([
                                        Forms\Components\Textarea::make('cirugias_previas')
                                            ->label('Cirugías Previas')
                                            ->rows(2),

                                        Forms\Components\Textarea::make('hospitalizaciones')
                                            ->label('Hospitalizaciones')
                                            ->rows(2),

                                        Forms\Components\Textarea::make('transfusiones')
                                            ->label('Transfusiones Sanguíneas')
                                            ->rows(2),

                                        Forms\Components\Textarea::make('habitos')
                                            ->label('Hábitos')
                                            ->helperText('Tabaco, alcohol, drogas, ejercicio, etc.')
                                            ->rows(2),
                                    ])
                                    ->columns(2),

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
