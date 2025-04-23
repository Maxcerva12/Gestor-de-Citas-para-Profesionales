<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums\FontWeight;
use App\Filament\Client\Pages\ProfessionalAvailability;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // Navigation and labels
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Profesionales';
    protected static ?string $navigationGroup = 'Gestión Administrativa';
    protected static ?string $label = 'Profesional';
    protected static ?string $pluralLabel = 'Profesionales';
    protected static ?string $recordTitleAttribute = 'name';

    // Navigation sorting
    protected static ?int $navigationSort = -10;

    // Default sort for data
    protected static ?string $defaultSort = 'name';

    public static function canCreate(): bool
    {
        return false; // Nadie puede crear usuarios desde este recurso
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
                                    ->description('Información básica del profesional')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('last_name')
                                                    ->label('Apellidos')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Grid::make(2),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\FileUpload::make('avatar_url')
                                                    ->label('Foto de perfil')
                                                    ->image()
                                                    ->directory('avatars')
                                                    ->visibility('public')
                                                    ->imageEditor(),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Dirección')
                                    ->description('Datos de ubicación')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('city')
                                                    ->label('Ciudad')
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('country')
                                                    ->label('País')
                                                    ->maxLength(100),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Información Profesional')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Section::make('Datos Profesionales')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('profession')
                                                    ->label('Profesión')
                                                    ->required()
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('especialty')
                                                    ->label('Especialidad')
                                                    ->required()
                                                    ->maxLength(100),
                                            ]),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Breve descripción profesional'),
                                        Forms\Components\KeyValue::make('custom_fields')
                                            ->label('Campos Personalizados')
                                            ->keyLabel('Campo')
                                            ->valueLabel('Valor')
                                            ->addButtonLabel('Añadir campo')
                                            ->reorderable(),
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
                    ->defaultImageUrl(fn($record): string => "https://ui-avatars.com/api/?name=" . urlencode($record->name . " " . $record->last_name) . "&color=FFFFFF&background=6366F1")
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($record) => $record->name . ' ' . $record->last_name),
                Tables\Columns\TextColumn::make('profession')
                    ->label('Profesión')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('especialty')
                    ->label('Especialidad')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('profession')
                    ->options(fn() => User::whereNotNull('profession')->distinct()->pluck('profession', 'profession')->toArray())
                    ->label('Profesión')
                    ->native(false),
                Tables\Filters\SelectFilter::make('especialty')
                    ->options(fn() => User::whereNotNull('especialty')->distinct()->pluck('especialty', 'especialty')->toArray())
                    ->label('Especialidad')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver perfil')
                    ->tooltip('Ver información completa del profesional')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->outlined()
                    ->size('sm')
                    ->extraAttributes([
                        'class' => 'font-medium transition ease-in-out duration-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 focus:ring-2 focus:ring-indigo-500/50',
                    ])
                    ->modalHeading('Perfil Profesional')
                    ->modalWidth('5xl')
                    ->modalIcon('heroicon-o-user-circle')
                    ->slideOver(),
                Tables\Actions\Action::make('view_availability')
                    ->label('Ver Disponibilidad')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    // ->url(fn($record) => route('filament.client.pages.professional-availability', ['professionalId' => $record->id]))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateDescription('Lista de profesionales disponibles.')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}