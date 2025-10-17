<?php

namespace App\Filament\Resources\MedicalHistoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use App\Models\ClinicalDocument;

class ClinicalDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'clinicalDocuments';

    protected static ?string $title = 'Documentos Clínicos';
    protected static ?string $modelLabel = 'Documento Clínico';
    protected static ?string $pluralModelLabel = 'Documentos Clínicos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Documento')
                    ->schema([
                        Forms\Components\Select::make('tipo_documento')
                            ->label('Tipo de Documento')
                            ->options(ClinicalDocument::TIPOS_DOCUMENTO)
                            ->required()
                            ->searchable()
                            ->native(false),

                        Forms\Components\TextInput::make('nombre_documento')
                            ->label('Nombre del Documento')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('fecha_documento')
                            ->label('Fecha del Documento')
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
                            ->helperText('Opcional: Asociar este documento con una cita registrada'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Archivo')
                    ->schema([
                        Forms\Components\FileUpload::make('archivo_path')
                            ->label('Archivo')
                            ->required()
                            ->directory('clinical-documents')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'image/*',
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText('Formatos aceptados: imágenes, PDF, Word. Tamaño máximo: 10MB')
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Obtener información del archivo
                                    $file = $state;
                                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                                        $set('archivo_nombre_original', $file->getClientOriginalName());
                                        $set('archivo_mime_type', $file->getMimeType());
                                        $set('archivo_size', $file->getSize());
                                    }
                                }
                            }),

                        Forms\Components\Hidden::make('archivo_nombre_original'),
                        Forms\Components\Hidden::make('archivo_mime_type'),
                        Forms\Components\Hidden::make('archivo_size'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Descripción y Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->helperText('Notas adicionales sobre este documento')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre_documento')
            ->columns([
                Tables\Columns\TextColumn::make('tipo_documento')
                    ->label('Tipo')
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ClinicalDocument::TIPOS_DOCUMENTO[$state] ?? $state
                    )
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'radiografia_panoramica', 'radiografia_periapical', 'radiografia_bite_wing', 'tomografia' => 'info',
                        'fotografia_intraoral', 'fotografia_extraoral' => 'warning',
                        'consentimiento_informado', 'consentimiento_cirugia', 'consentimiento_endodoncia', 'consentimiento_ortodoncia', 'consentimiento_fluor' => 'success',
                        'laboratorio', 'receta', 'formula_medica' => 'primary',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre_documento')
                    ->label('Nombre')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40),

                Tables\Columns\IconColumn::make('is_image')
                    ->label('Imagen')
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('file_size_formatted')
                    ->label('Tamaño')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('fecha_documento')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Subido por')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Carga')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_documento', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options(ClinicalDocument::TIPOS_DOCUMENTO)
                    ->multiple(),

                Tables\Filters\Filter::make('fecha_documento')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_documento', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_documento', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_image')
                    ->label('Tipo de Archivo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Imágenes')
                    ->falseLabel('Solo Documentos')
                    ->queries(
                        true: fn(Builder $query) => $query->where('archivo_mime_type', 'like', 'image/%'),
                        false: fn(Builder $query) => $query->where('archivo_mime_type', 'not like', 'image/%'),
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Subir Documento')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Asegurar que se capture la información del archivo
                        if (isset($data['archivo_path'])) {
                            $path = $data['archivo_path'];
                            if (Storage::exists($path)) {
                                $data['archivo_nombre_original'] = basename($path);
                                $data['archivo_mime_type'] = Storage::mimeType($path);
                                $data['archivo_size'] = Storage::size($path);
                            }
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(ClinicalDocument $record): string => Storage::url($record->archivo_path))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('preview')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(fn(ClinicalDocument $record) => view('filament.modals.document-preview', ['record' => $record]))
                    ->modalWidth('7xl')
                    ->visible(fn(ClinicalDocument $record): bool => $record->is_image || str_contains($record->archivo_mime_type, 'pdf')),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (ClinicalDocument $record) {
                        // El archivo se elimina automáticamente en el modelo
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
