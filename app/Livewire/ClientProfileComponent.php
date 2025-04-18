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
use App\Models\Client;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ClientProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 15;

    public Model $user;

    public function mount(): void
    {
        $this->user = Client::findOrFail(Auth::guard('client')->id());
        $this->form->fill($this->user->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->aside()
                    ->description('Estos datos serán visibles en tu perfil.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country')
                            ->label('País')
                            ->maxLength(255),
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
        return view('livewire.client-profile-component');
    }
}
