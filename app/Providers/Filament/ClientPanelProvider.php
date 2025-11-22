<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\MenuItem;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Awcodes\LightSwitch\LightSwitchPlugin;
use Awcodes\LightSwitch\Enums\Alignment;
use Cmsmaxinc\FilamentErrorPages\FilamentErrorPagesPlugin;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('9rem')
            ->id('client')
            ->path('client')
            ->viteTheme('resources/css/filament/client/theme.css')
            ->colors([
                'primary' => '#ebb619',
                'secondary' => Color::Indigo,
                'success' => Color::Green,
                'danger' => Color::Red,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->login()
            ->brandName('Fundacion Odontológica Zoila Padilla')
            ->brandLogo(asset('storage\img\lg_zoila_padilla2.svg'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('storage\img\favicon.ico'))
            ->registration(\App\Filament\Client\Pages\Auth\Register::class)
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                AuthUIEnhancerPlugin::make()
                ->showEmptyPanelOnMobile(false)
                ->formPanelPosition('right')
                ->formPanelWidth('50%')
                ->emptyPanelBackgroundImageOpacity('70%')
                ->emptyPanelBackgroundColor(Color::hex("#d0b4ca"))
                ->emptyPanelBackgroundImageUrl(asset('storage/img/operacion.jpg')),
                FilamentErrorPagesPlugin::make()
                    ->routes([
                        'client/*',
                    ]),
                LightSwitchPlugin::make()
                    ->position(Alignment::TopRight),
                FilamentFullCalendarPlugin::make()
                    ->selectable(false) // Cambiado a false para evitar selección
                    ->editable(false)   // Cambiado a false para evitar edición
                    ->timezone(config('app.timezone')) // Usa la zona horaria de la app
                    ->locale(config('app.locale')) // Usa el idioma de la app
                    ->schedulerLicenseKey('GPL-My-Project-Is-Open-Source'), // Licencia GPL para proyectos open source

                FilamentEditProfilePlugin::make()
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // Especifica el directorio
                        rules: 'image|mimes:jpeg,png,jpg|max:2048' // Define las reglas
                    )
                    ->slug('profile')
                    ->setNavigationGroup('Configuración')
                    ->setIcon('heroicon-o-user-circle')
                    ->setNavigationLabel('Mi Perfil')
                    ->shouldShowAvatarForm()
                    ->shouldShowSanctumTokens()
                    ->shouldShowBrowserSessionsForm()
                    ->customProfileComponents([
                        \App\Livewire\ClientProfileComponent::class,
                    ]),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => Auth::user()->name) // Usar el facade de Auth
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ])
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
            ->widgets([
                \App\Filament\Client\Widgets\ClientWelcomeWidget::class,
                \App\Filament\Client\Widgets\ClientStatsOverviewWidget::class,
                \App\Filament\Client\Widgets\NextAppointmentWidget::class,
                \App\Filament\Client\Widgets\HealthSummaryWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('client')// Usar el guardia 'client'
            ->authPasswordBroker('clients')// Usar el broker 'clients' para password reset
            ->passwordReset()
            ->emailVerification()
            ->databaseNotifications();
    }
}
