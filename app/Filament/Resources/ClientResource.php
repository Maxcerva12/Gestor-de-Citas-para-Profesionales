<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ClientResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Actions\Action;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    // Navigation and labels
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $navigationGroup = 'Gestión Administrativa';
    protected static ?string $label = 'Cliente';
    protected static ?string $pluralLabel = 'Clientes';
    protected static ?string $recordTitleAttribute = 'name';

    // Navigation sorting
    protected static ?int $navigationSort = -5;

    // Default sort for data
    protected static ?string $defaultSort = 'name';

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

    // Permission checks
    public static function canViewAny(): bool
    {
        return Auth::check() && Gate::allows('view_any_client');
    }

    public static function canView(Model $record): bool
    {
        return Auth::check() && Gate::allows('view_client', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Gate::allows('create_client');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Gate::allows('update_client', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::check() && Gate::allows('delete_client', $record);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::check() && Gate::allows('delete_any_client');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('view_any_client');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'apellido', 'email', 'phone', 'numero_documento', 'city', 'aseguradora', 'ocupacion'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información Personal')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Section::make('Datos Básicos')
                                    ->description('Información principal del paciente')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('apellido')
                                                    ->label('Apellido')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\FileUpload::make('avatar_url')
                                                    ->label('Foto de perfil')
                                                    ->image()
                                                    ->directory('client-avatars')
                                                    ->visibility('public')
                                                    ->imageEditor()
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('email')
                                                    ->label('Correo Electrónico')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true),
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Teléfono')
                                                    ->tel()
                                                    ->prefix('+57')
                                                    ->telRegex('/^[0-9\s\-\+\(\)]+$/')
                                                    ->maxLength(20)
                                                    ->helperText('Formato: 3XX XXX XXXX'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Documentación')
                                    ->description('Información de identificación del paciente')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('tipo_documento')
                                                    ->label('Tipo de Documento')
                                                    ->native(false)
                                                    ->required()
                                                    ->options([
                                                        'CC' => 'Cédula de Ciudadanía',
                                                        'CE' => 'Cédula de Extranjería',
                                                        'TI' => 'Tarjeta de Identidad',
                                                        'PP' => 'Pasaporte',
                                                    ])
                                                    ->default('CC'),
                                                Forms\Components\TextInput::make('numero_documento')
                                                    ->label('Número de Documento')
                                                    ->required()
                                                    ->numeric()
                                                    ->maxLength(50)
                                                    ->unique(ignoreRecord: true),
                                                Forms\Components\Select::make('genero')
                                                    ->label('Género')
                                                    ->native(false)
                                                    ->options([
                                                        'Masculino' => 'Masculino',
                                                        'Femenino' => 'Femenino',
                                                        'Otro' => 'Otro',
                                                    ])
                                                    ->required(),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\DatePicker::make('fecha_nacimiento')
                                                    ->label('Fecha de Nacimiento')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y')
                                                    ->required()
                                                    ->maxDate(now())
                                                    ->helperText('La edad se calculará automáticamente'),
                                                Forms\Components\TextInput::make('ocupacion')
                                                    ->label('Ocupación')
                                                    ->maxLength(255)
                                                    ->placeholder('Ej: Estudiante, Profesor, Ingeniero...'),
                                                Forms\Components\Select::make('aseguradora')
                                                    ->label('EPS/Aseguradora')
                                                    ->options([
                                                        'Sura EPS' => 'Sura EPS',
                                                        'Nueva EPS' => 'Nueva EPS',
                                                        'Sanitas EPS' => 'Sanitas EPS',
                                                        'Compensar EPS' => 'Compensar EPS',
                                                        'Famisanar EPS' => 'Famisanar EPS',
                                                        'EPS Coomeva' => 'EPS Coomeva',
                                                        'Salud Total EPS' => 'Salud Total EPS',
                                                        'Medimás EPS' => 'Medimás EPS',
                                                        'Aliansalud EPS' => 'Aliansalud EPS',
                                                        'EPS SOS' => 'EPS SOS',
                                                        'Coosalud EPS' => 'Coosalud EPS',
                                                        'Mutual SER EPS' => 'Mutual SER EPS',
                                                        'Capital Salud EPS' => 'Capital Salud EPS',
                                                        'Saludvida EPS' => 'Saludvida EPS',
                                                        'Emdisalud ESI' => 'Emdisalud ESI',
                                                        'Emssanar ESI' => 'Emssanar ESI',
                                                        'Cajacopi Atlantico' => 'Cajacopi Atlantico',
                                                        'Capresoca EPS' => 'Capresoca EPS',
                                                        'Comfachocó EPS' => 'Comfachocó EPS',
                                                        'EPS Régimen Especial' => 'EPS Régimen Especial',
                                                        'Particular/Prepagada' => 'Particular/Prepagada',
                                                    ])
                                                    ->searchable()
                                                    ->allowHtml(false)
                                                    ->placeholder('Seleccione una EPS')
                                                    ->native(false)
                                                    ->createOptionForm([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Nombre de la EPS/Aseguradora')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->placeholder('Ingrese el nombre completo de la EPS')
                                                    ])
                                                    ->createOptionUsing(function (array $data): string {
                                                        return $data['name'];
                                                    })
                                                    ->createOptionAction(function (Action $action) {
                                                        return $action
                                                            ->modalHeading('Crear nueva EPS/Aseguradora')
                                                            ->modalSubmitActionLabel('Crear')
                                                            ->modalWidth('lg');
                                                    })
                                                    ->helperText('Si no encuentra la EPS en la lista, puede crearla haciendo clic en "+"'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Contacto de Emergencia')
                                    ->description('Persona a contactar en caso de emergencia')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('nombre_contacto_emergencia')
                                                    ->label('Nombre de Contacto de Emergencia')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('telefono_contacto_emergencia')
                                                    ->label('Teléfono de Contacto de Emergencia')
                                                    ->tel()
                                                    ->maxLength(20),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Ubicación')
                                    ->description('Datos de dirección del paciente')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->label('Dirección')
                                            ->rows(2)
                                            ->maxLength(255),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('city')
                                                    ->label('Ciudad')
                                                    ->maxLength(100),
                                                Forms\Components\Select::make('country')
                                                    ->label('País')
                                                    ->searchable()
                                                    ->default('Colombia')
                                                    ->options([
                                                        'Colombia' => 'Colombia',
                                                        'España' => 'España',
                                                        'Argentina' => 'Argentina',
                                                        'Chile' => 'Chile',
                                                        'Ecuador' => 'Ecuador',
                                                        'México' => 'México',
                                                        'Perú' => 'Perú',
                                                        'Venezuela' => 'Venezuela',
                                                        'Otro' => 'Otro',
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Cuenta')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Forms\Components\Section::make('Credenciales')
                                    ->description('Datos de acceso del cliente')
                                    ->schema([
                                        Forms\Components\TextInput::make('password')
                                            ->label('Contraseña')
                                            ->password()
                                            ->revealable()


                                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->helperText('Dejar en blanco para mantener la contraseña actual')
                                            ->confirmed(),
                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->label('Confirmar Contraseña')
                                            ->password()
                                            ->revealable()
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->dehydrated(false),
                                    ]),
                                Forms\Components\Section::make('Información Adicional')
                                    ->description('Datos adicionales para la gestión del cliente')
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\Toggle::make('active')
                                            ->label('Cliente Activo')
                                            ->default(true)
                                            ->inline(false)
                                            ->helperText('Desactive para desactivar el acceso del cliente al sistema'),
                                        Forms\Components\KeyValue::make('custom_fields')
                                            ->label('Campos Personalizados')
                                            ->keyLabel('Campo')
                                            ->valueLabel('Valor')
                                            ->addButtonLabel('Añadir campo')
                                            ->reorderable()
                                            ->helperText('Campos adicionales específicos para este cliente'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notas')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\RichEditor::make('notes')
                                            ->label('Notas Internas')
                                            ->disableToolbarButtons([
                                                'attachFiles',
                                            ])
                                            ->helperText('Estas notas son solo visibles para los administradores'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record): string => "https://ui-avatars.com/api/?name=" . urlencode($record->name) . "&color=FFFFFF&background=3B82F6")
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($record) => $record->name . ' ' . ($record->apellido ?? '')),

                Tables\Columns\TextColumn::make('numero_documento')
                    ->label('Documento')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn($record) =>
                        ($record->tipo_documento ?? 'CC') . ': ' . ($record->numero_documento ?? 'Sin registro')
                    )
                    ->icon('heroicon-m-identification'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-phone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('aseguradora')
                    ->label('EPS')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ocupacion')
                    ->label('Ocupación')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('No especificada')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Edad')
                    ->date('d/m/Y')
                    ->sortable()
                    ->formatStateUsing(
                        fn($record) =>
                        $record->fecha_nacimiento ?
                        \Carbon\Carbon::parse($record->fecha_nacimiento)->age . ' años' :
                        'Sin registro'
                    )
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Estado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aseguradora')
                    ->label('EPS/Aseguradora')
                    ->native(false)
                    ->placeholder('Todas las aseguradoras')
                    ->options(function () {
                        return Cache::remember('client_aseguradoras_filter', 3600, function () {
                            return Client::query()
                                ->whereNotNull('aseguradora')
                                ->where('aseguradora', '!=', '')
                                ->distinct()
                                ->orderBy('aseguradora')
                                ->pluck('aseguradora', 'aseguradora')
                                ->toArray();
                        });
                    }),

                Tables\Filters\SelectFilter::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->native(false)
                    ->placeholder('Todos los tipos')
                    ->options([
                        'CC' => 'Cédula de Ciudadanía',
                        'CE' => 'Cédula de Extranjería',
                        'TI' => 'Tarjeta de Identidad',
                        'PP' => 'Pasaporte',
                    ]),

                Tables\Filters\SelectFilter::make('genero')
                    ->label('Género')
                    ->native(false)
                    ->placeholder('Todos los géneros')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'Otro' => 'Otro',
                    ]),

                Tables\Filters\SelectFilter::make('country')
                    ->label('País')
                    ->native(false)
                    ->placeholder('Todos los países')
                    ->options(function () {
                        return Cache::remember('client_countries_filter', 3600, function () {
                            return Client::query()
                                ->whereNotNull('country')
                                ->where('country', '!=', '')
                                ->distinct()
                                ->orderBy('country')
                                ->pluck('country', 'country')
                                ->toArray();
                        });
                    }),

                Tables\Filters\TernaryFilter::make('active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->native(false)
                    ->queries(
                        true: fn($query) => $query->where('active', true),
                        false: fn($query) => $query->where('active', false),
                        blank: fn($query) => $query,
                    ),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->native(false)
                            ->label('Registrado desde')
                            ->placeholder('dd/mm/yyyy')
                            ->maxDate(fn(callable $get) => $get('created_until')),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->label('Registrado hasta')
                            ->placeholder('dd/mm/yyyy')
                            ->minDate(fn(callable $get) => $get('created_from')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                function ($q, $date) {
                                    $date = preg_replace('/\s+00:00:00$/', '', $date); // Elimina hora extra si viene
                                    return $q->where('created_at', '>=', $date . ' 00:00:00');
                                }
                            )
                            ->when(
                                $data['created_until'],
                                function ($q, $date) {
                                    $date = preg_replace('/\s+00:00:00$/', '', $date); // Elimina hora extra si viene
                                    return $q->where('created_at', '<=', $date . ' 23:59:59');
                                }
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Desde: ' . \Carbon\Carbon::parse(preg_replace('/\s+00:00:00$/', '', $data['created_from']))->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Hasta: ' . \Carbon\Carbon::parse(preg_replace('/\s+00:00:00$/', '', $data['created_until']))->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('has_allergies')
                    ->label('Alergias')
                    ->placeholder('Todos')
                    ->trueLabel('Con alergias')
                    ->falseLabel('Sin alergias')
                    ->native(false)
                    ->queries(
                        true: fn($query) => $query->whereNotNull('alergias')->where('alergias', '!=', ''),
                        false: fn($query) => $query->where(fn($q) => $q->whereNull('alergias')->orWhere('alergias', '')),
                        blank: fn($query) => $query,
                    ),

                Tables\Filters\TernaryFilter::make('has_medical_history')
                    ->label('Historial Médico')
                    ->placeholder('Todos')
                    ->trueLabel('Con historial')
                    ->falseLabel('Sin historial')
                    ->native(false)
                    ->queries(
                        true: fn($query) => $query->whereNotNull('historial_medico')->where('historial_medico', '!=', ''),
                        false: fn($query) => $query->where(fn($q) => $q->whereNull('historial_medico')->orWhere('historial_medico', '')),
                        blank: fn($query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('gray'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
                    ->tooltip('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportToCsv')
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            // Create a temporary file
                            $csv = fopen('php://temp', 'r+');

                            // Add CSV headers
                            fputcsv($csv, [
                                'ID',
                                'Nombre',
                                'Email',
                                'Teléfono',
                                'Dirección',
                                'Ciudad',
                                'País',
                                'Estado',
                                'Fecha de Registro'
                            ]);

                            // Add data for each selected record
                            foreach ($records as $record) {
                                fputcsv($csv, [
                                    $record->id,
                                    $record->name,
                                    $record->email,
                                    $record->phone,
                                    $record->address ?? '',
                                    $record->city ?? '',
                                    $record->country ?? '',
                                    $record->active ? 'Activo' : 'Inactivo',
                                    $record->created_at->format('d/m/Y H:i')
                                ]);
                            }

                            // Reset the pointer to the beginning of the file
                            rewind($csv);

                            // Get the content of the file
                            $content = stream_get_contents($csv);
                            fclose($csv);

                            // Generate a filename with current date
                            $filename = 'clientes_' . now()->format('Y-m-d_His') . '.csv';

                            // Return a download response
                            return response()->streamDownload(function () use ($content) {
                                echo $content;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                            ]);
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Cliente'),
            ])
            ->emptyStateDescription('Comienza agregando un nuevo cliente a tu sistema.')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AppointmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
