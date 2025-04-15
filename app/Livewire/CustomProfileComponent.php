<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 15;

    public Model $user;
    public function mount(): void
    {
        // abort_unless(Auth::check() && Gate::allows('edit_profile', Auth::user()), 403);

        $this->user = User::findOrFail(Auth::id());
        $this->user = User::findOrFail(Auth::id());
        $this->form->fill($this->user->attributesToArray());
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->aside()
                    ->description('Estos datos serán visibles para otros usuarios.')
                    ->schema([

                        Forms\Components\TextInput::make('last_name')->label('Apellido')->required()->maxLength(255),

                        Forms\Components\TextInput::make('phone')->label('Teléfono')->required()->maxLength(255),
                        Forms\Components\TextInput::make('address')->label('Dirección')->required()->maxLength(255),
                        Forms\Components\Select::make('document_type')
                            ->label('Tipo de Documento')
                            ->required()
                            ->native(false)
                            ->options([
                                'CC' => 'Cédula de Ciudadanía',
                                'CE' => 'Cédula de Extranjería',
                                'TI' => 'Tarjeta de Identidad',
                                'PP' => 'Pasaporte',
                            ]),
                        Forms\Components\TextInput::make('document_number')->label('Número de Documento')->required()->maxLength(255),
                    ]),

                Section::make('Información Profesional')
                    ->aside()
                    ->description('Proporcione información relacionada con su experiencia profesional.')
                    ->columns(2)
                    ->schema([
                        // Forms\Components\TextInput::make('professional_license')->label('Licencia Profesional')->maxLength(255)->helperText('Este campo es opcional y solo aplicable para doctores.'),
                        Forms\Components\TextInput::make('speciality')->label('Especialidad')->maxLength(255)->helperText('Este campo es opcional y solo aplicable para doctores.'),
                        // Forms\Components\Select::make('status')
                        //     ->label('Estado')
                        //     ->columnSpanFull()
                        //     ->native(false)
                        //     ->required()
                        //     ->default('active')
                        //     ->options([
                        //         'active' => 'Activo',
                        //         'inactive' => 'Inactivo',
                        //         'on_leave' => 'En Permiso',
                        //     ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->user->update($data);

        Notification::make()
            ->success()
            ->title('¡Actualización Exitosa!')
            ->body('Los cambios en tu perfil han sido guardados correctamente.')
            ->duration(5000)  // Duración en milisegundos
            ->send();
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
