<?php

namespace App\Filament\Client\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        // Campos esenciales para el registro
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        
                        // Sección obligatoria de tratamiento de datos
                        $this->getDataTreatmentSection(),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nombre')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Correo Electrónico')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Contraseña')
            ->password()
            ->revealable()
            ->required()
            ->dehydrateStateUsing(fn($state) => bcrypt($state))
            ->same('passwordConfirmation')
            ->minLength(8);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirmar Contraseña')
            ->password()
            ->revealable()
            ->required()
            ->dehydrated(false);
    }

    protected function getDataTreatmentSection(): Component
    {
        return Section::make('Autorización para Tratamiento de Datos Personales')
            ->icon('heroicon-o-shield-check')
            ->schema([
                Placeholder::make('data_treatment_info')
                    ->label('Autorización para el Tratamiento de Datos Personales')
                    ->content('De conformidad con la Ley 1581 de 2012 y el Decreto 1377 de 2013, la Fundación Odontológica Zoila Padilla requiere su autorización previa, expresa e informada para realizar el tratamiento de sus datos personales.

Sus datos serán utilizados para la gestión y programación de citas médicas, creación y mantenimiento de su historia clínica, procesos de facturación y gestión administrativa. También podremos comunicarnos con usted sobre servicios, recordatorios de citas y promociones, siempre cumpliendo con las obligaciones legales del sector salud y mejorando continuamente nuestros servicios profesionales.

Como titular de sus datos personales, usted tiene derecho a conocer, actualizar y rectificar la información que tenemos sobre usted. Puede solicitar prueba de la autorización que nos ha otorgado y ser informado sobre el uso que damos a sus datos. Si considera necesario, puede presentar quejas ante la Superintendencia de Industria y Comercio, revocar la autorización otorgada, solicitar la supresión de sus datos o acceder gratuitamente a toda la información personal que manejamos.

Para ejercer cualquiera de estos derechos, puede contactarnos a través de nuestro correo electrónico contacto@fundacionzoilapadilla.com o presentando una solicitud escrita directamente en nuestras instalaciones.

Es importante que tenga en cuenta que la información médica será tratada con especial protección según la normativa vigente. La respuesta a nuestras preguntas es voluntaria, excepto aquellos datos que son indispensables para brindarle el servicio odontológico que solicita.'),
                
                Grid::make(1)
                    ->schema([
                        Checkbox::make('accepts_data_treatment')
                            ->label('AUTORIZO el tratamiento de mis datos personales')
                            ->helperText('Otorgo mi consentimiento previo, expreso e informado para que la Fundación Odontológica Zoila Padilla trate mis datos conforme a las finalidades descritas anteriormente.')
                            ->required()
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Debe autorizar el tratamiento de datos personales para continuar.',
                            ]),
                        
                        Checkbox::make('accepts_privacy_policy')
                            ->label('ACEPTO las políticas de tratamiento de datos')
                            ->helperText('Declaro haber leído y acepto las políticas de tratamiento de información, así como los procedimientos para ejercer mis derechos como titular de datos.')
                            ->required()
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Debe aceptar las políticas de tratamiento de datos para continuar.',
                            ]),
                        
                        // Checkbox::make('accepts_commercial_communications')
                        //     ->label('📧 Acepto recibir comunicaciones comerciales (OPCIONAL)')
                        //     ->helperText('Autorizo el envío de información promocional, newsletter, recordatorios y comunicaciones relacionadas con los servicios odontológicos')
                        //     ->default(false)
                        //     ->extraAttributes(['class' => 'text-base']),
                    ]),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    protected function handleRegistration(array $data): Model
    {
        // Validar que los campos de tratamiento de datos estén presentes y sean verdaderos
        if (!isset($data['accepts_data_treatment']) || !$data['accepts_data_treatment']) {
            throw new \Illuminate\Validation\ValidationException(
                validator: validator($data, []),
                errorBag: 'accepts_data_treatment',
                messages: ['accepts_data_treatment' => 'Debe autorizar el tratamiento de datos personales para continuar con el registro.']
            );
        }

        if (!isset($data['accepts_privacy_policy']) || !$data['accepts_privacy_policy']) {
            throw new \Illuminate\Validation\ValidationException(
                validator: validator($data, []),
                errorBag: 'accepts_privacy_policy', 
                messages: ['accepts_privacy_policy' => 'Debe aceptar la política de privacidad para continuar con el registro.']
            );
        }

        // Agregar campos automáticos para el tratamiento de datos
        $data['data_treatment_date'] = now();
        $data['active'] = true;

        return $this->getUserModel()::create($data);
    }

    protected function getUserModel(): string
    {
        return config('auth.providers.clients.model');
    }
}