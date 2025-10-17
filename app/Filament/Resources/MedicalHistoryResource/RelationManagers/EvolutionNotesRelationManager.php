<?php

namespace App\Filament\Resources\MedicalHistoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvolutionNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'evolutionNotes';

    protected static ?string $title = 'Notas de Evolución';
    protected static ?string $modelLabel = 'Nota de Evolución';
    protected static ?string $pluralModelLabel = 'Notas de Evolución';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Consulta')
                    ->schema([
                        Forms\Components\DateTimePicker::make('fecha_nota')
                            ->label('Fecha y Hora de la Consulta')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\Select::make('appointment_id')
                            ->label('Cita Asociada')
                            ->relationship('appointment', 'id')
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                "Cita #{$record->id} - " . $record->start_time->format('d/m/Y H:i')
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Opcional: Asociar esta nota con una cita registrada'),

                        Forms\Components\TextInput::make('profesional_nombre')
                            ->label('Profesional')
                            ->default(fn() => auth()->user()->name)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Motivo y Síntomas')
                    ->schema([
                        Forms\Components\Textarea::make('motivo_consulta')
                            ->label('Motivo de Consulta')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('sintomas')
                            ->label('Síntomas Reportados')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Evaluación Clínica')
                    ->schema([
                        Forms\Components\Textarea::make('examen_clinico')
                            ->label('Examen Clínico')
                            ->helperText('Hallazgos del examen físico y odontológico')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('diagnostico')
                            ->label('Diagnóstico')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Tratamiento e Indicaciones')
                    ->schema([
                        Forms\Components\Textarea::make('tratamiento_realizado')
                            ->label('Tratamiento Realizado')
                            ->helperText('Procedimientos y tratamientos aplicados en esta consulta')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('medicamentos_recetados')
                            ->label('Medicamentos Recetados')
                            ->helperText('Fórmula médica, dosis y duración')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('indicaciones')
                            ->label('Indicaciones al Paciente')
                            ->helperText('Recomendaciones, cuidados post-tratamiento, etc.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Seguimiento')
                    ->schema([
                        Forms\Components\DatePicker::make('proxima_cita')
                            ->label('Próxima Cita Sugerida')
                            ->minDate(now()),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones Adicionales')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fecha_nota')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_nota')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('motivo_consulta')
                    ->label('Motivo de Consulta')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('diagnostico')
                    ->label('Diagnóstico')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tratamiento_realizado')
                    ->label('Tratamiento')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('profesional_nombre')
                    ->label('Profesional')
                    ->searchable(),

                Tables\Columns\TextColumn::make('proxima_cita')
                    ->label('Próxima Cita')
                    ->date('d/m/Y')
                    ->toggleable(),
            ])
            ->defaultSort('fecha_nota', 'desc')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_nota', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_nota', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva Nota de Evolución')
                    ->icon('heroicon-o-plus'),
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
}
