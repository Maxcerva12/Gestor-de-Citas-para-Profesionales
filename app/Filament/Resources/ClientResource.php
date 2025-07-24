<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use App\Forms\Components\Odontogram;
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
        return ['name', 'email', 'phone', 'city'];
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
                                    ->description('Información principal del cliente')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nombre Completo')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(2),
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
                                                    ->maxLength(20),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Ubicación')
                                    ->description('Datos de dirección del cliente')
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
                                                    ->options([
                                                        'España' => 'España',
                                                        'Argentina' => 'Argentina',
                                                        'Chile' => 'Chile',
                                                        'Colombia' => 'Colombia',
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
                                            ->helperText('Desactive para bloquear el acceso del cliente'),
                                        Forms\Components\KeyValue::make('custom_fields')
                                            ->label('Campos Personalizados')
                                            ->keyLabel('Campo')
                                            ->valueLabel('Valor')
                                            ->addButtonLabel('Añadir campo')
                                            ->reorderable(),
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
                        Forms\Components\Tabs\Tab::make('Odontograma')
                            ->icon('heroicon-o-face-smile')
                            ->schema([
                                Forms\Components\Section::make('Información Dental')
                                    ->description('Datos odontológicos del paciente')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('last_dental_visit')
                                                    ->label('Última Visita Dental')
                                                    ->displayFormat('d/m/Y')
                                                    ->native(false),
                                                Forms\Components\Textarea::make('dental_notes')
                                                    ->label('Notas Dentales')
                                                    ->rows(3)
                                                    ->helperText('Observaciones generales sobre el estado dental'),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Odontograma Interactivo')
                                    ->description('Haz clic en los dientes para actualizar su estado')
                                    ->schema([
                                        Odontogram::make('odontogram')
                                            ->label('')
                                            ->showPermanent(true)
                                            ->showTemporary(true),
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
                    ->weight(FontWeight::Bold),
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
                // Tables\Filters\TrashedFilter::make()
                //     ->visible(fn() => Auth::user()->can('restore_client')),

                Tables\Filters\SelectFilter::make('country')
                    ->label('País')
                    ->native(false)
                    ->options(function () {
                        $countries = Client::distinct()
                            ->pluck('country', 'country')
                            ->map(function ($country) {
                                return $country ?? 'Sin especificar';
                            })
                            ->toArray();

                        return $countries;
                    }),
                Tables\Filters\Filter::make('active')
                    ->label('Estado')
                    ->toggle(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->native(false)
                            ->label('Registrado desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->label('Registrado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
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
            // 'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
