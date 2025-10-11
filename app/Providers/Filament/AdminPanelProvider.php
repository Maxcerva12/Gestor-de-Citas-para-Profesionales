<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\MenuItem;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use App\Filament\Widgets\TotalAppointmentsChart;
use App\Filament\Widgets\AppointmentsByStatusChart;
use App\Filament\Pages\Auth\CustomLogin;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('9rem')
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Fundacion Odontológica Zoila Padilla')
            ->login(CustomLogin::class)
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Blue,
                'secondary' => Color::Indigo,
                'success' => Color::Green,
                'danger' => Color::Red,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // \App\Filament\Widgets\StatsProfessionalsOverview::class,
                // \App\Filament\Widgets\StatsClientsOverview::class,
                \App\Filament\Widgets\CombinedStatsOverview::class,
                TotalAppointmentsChart::class,
                AppointmentsByStatusChart::class,


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
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentFullCalendarPlugin::make()
                    ->selectable(true)
                    ->editable(true)
                    ->timezone(config('app.timezone'))
                    ->locale(config('app.locale'))
                    ->schedulerLicenseKey('GPL-My-Project-Is-Open-Source'),
                FilamentEditProfilePlugin::make()
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
                        rules: 'image|mimes:jpeg,png,jpg|max:2048'
                    )
                    ->slug('profile')
                    ->setNavigationGroup('Configuración')
                    ->setIcon('heroicon-o-user-circle')
                    ->setNavigationLabel('Mi Perfil')
                    ->shouldShowAvatarForm()
                    ->shouldShowSanctumTokens()
                    ->shouldShowBrowserSessionsForm()
                    ->customProfileComponents([
                        \App\Livewire\CustomProfileComponent::class,
                    ]),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => Auth::user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),
            ]);
    }
}
