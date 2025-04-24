<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Resources\Pages\Page;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Filament\Client\Resources\UserResource\Widgets\CalendarWidget;
use Livewire\Attributes\On;

class ViewAvailability extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.client.resources.user-resource.pages.view-availability';

    // El profesional seleccionado
    public User $record;

    // Variable para almacenar el formulario de reserva
    public ?array $appointment = [];

    // Variables para el control de la reserva
    public $scheduleId = null;
    public $appointmentDate = null;
    public $startTime = null;
    public $endTime = null;

    public function mount(User $record)
    {
        $this->record = $record;
        $this->appointment = [
            'professional_id' => $record->id,
            'client_id' => auth()->user()->id,
            'date' => null,
            'start_time' => null,
            'end_time' => null,
            'notes' => null,
        ];

        // Verificar si este profesional tiene horarios disponibles
        $availableSlots = Schedule::where('user_id', $record->id)
            ->where('is_available', true)
            ->count();

        Log::info("Profesional ID {$record->id} tiene {$availableSlots} horarios disponibles");

        if ($availableSlots === 0) {
            Notification::make()
                ->title('Sin horarios disponibles')
                ->body('Este profesional no tiene horarios disponibles por el momento.')
                ->warning()
                ->send();
        }
    }

    // Método para recibir el evento de reserva desde el calendario
    #[On('book-appointment')]
    public function handleBookAppointment($data)
    {
        Log::info('Recibido evento book-appointment con datos: ' . json_encode($data));

        // Guardar los datos de la cita
        $this->scheduleId = $data['scheduleId'] ?? null;
        $this->appointmentDate = $data['date'] ?? null;
        $this->startTime = $data['startTime'] ?? null;
        $this->endTime = $data['endTime'] ?? null;

        // Actualizar los datos del formulario de reserva
        $this->appointment = [
            'professional_id' => $this->record->id,
            'client_id' => auth()->user()->id,
            'date' => $this->appointmentDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'notes' => null,
        ];

        // Mostrar el formulario de reserva usando la acción
        $this->mountAction('bookAppointment');
    }

    // Método para personalizar los widgets de la página
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // Método para mostrar el formulario de reserva de cita
    public function bookAppointment($date, $startTime, $endTime)
    {
        $this->appointment['date'] = $date;
        $this->appointment['start_time'] = $startTime;
        $this->appointment['end_time'] = $endTime;

        $this->dispatch('open-modal', id: 'book-appointment-modal');
    }

    // Formulario para reservar cita
    public function getBookAppointmentForm(): Form
    {
        return Form::make($this)
            ->schema([
                Forms\Components\DatePicker::make('appointment.date')
                    ->label('Fecha')
                    ->required()
                    ->disabled()
                    ->default($this->appointment['date']),
                Forms\Components\TextInput::make('appointment.start_time')
                    ->label('Hora de inicio')
                    ->required()
                    ->disabled()
                    ->default($this->appointment['start_time']),
                Forms\Components\TextInput::make('appointment.end_time')
                    ->label('Hora de fin')
                    ->required()
                    ->disabled()
                    ->default($this->appointment['end_time']),
                Forms\Components\Textarea::make('appointment.notes')
                    ->label('Notas o detalles adicionales')
                    ->placeholder('Describe brevemente el motivo de tu cita...')
                    ->maxLength(500),
            ]);
    }

    // Método para procesar la reserva
    public function submitBooking()
    {
        // Validar datos
        $validated = $this->validate([
            'appointment.date' => 'required|date',
            'appointment.start_time' => 'required',
            'appointment.end_time' => 'required',
            'appointment.notes' => 'nullable|string|max:500',
        ]);

        // Aquí redireccionaríamos a la pasarela de pago
        // Por ahora, simplemente redirigimos a una función que simula el pago exitoso
        return redirect()->route('client.payment.process', [
            'professional_id' => $this->record->id,
            'date' => $this->appointment['date'],
            'start_time' => $this->appointment['start_time'],
            'end_time' => $this->appointment['end_time'],
            'notes' => $this->appointment['notes'],
        ]);
    }

    // Añade este método en tu clase
    protected function getActions(): array
    {
        return [
            Action::make('bookAppointment')
                ->label('Reservar cita')
                ->form([
                    Forms\Components\DatePicker::make('date')
                        ->label('Fecha')
                        ->required()
                        ->disabled()
                        ->default($this->appointmentDate),
                    Forms\Components\TextInput::make('start_time')
                        ->label('Hora de inicio')
                        ->required()
                        ->disabled()
                        ->default($this->startTime),
                    Forms\Components\TextInput::make('end_time')
                        ->label('Hora de fin')
                        ->required()
                        ->disabled()
                        ->default($this->endTime),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas o detalles adicionales')
                        ->placeholder('Describe brevemente el motivo de tu cita...')
                        ->maxLength(500),
                ])
                ->action(function (array $data): void {
                    // Actualizar los datos del formulario
                    $this->appointment['notes'] = $data['notes'] ?? null;
                    
                    // Proceder con la reserva
                    $this->submitBooking();
                })
                ->hidden() // Lo ocultamos porque lo llamaremos programáticamente
                ->modalHeading('Reservar cita con ' . $this->record->name)
                ->modalSubmitActionLabel('Confirmar reserva')
        ];
    }
}