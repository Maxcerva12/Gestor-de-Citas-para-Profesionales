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
use App\Filament\Widgets\RevenueOverviewWidget;
use App\Filament\Widgets\MonthlyRevenueChart;
use App\Filament\Widgets\TopProfessionalsChart;
use Awcodes\LightSwitch\LightSwitchPlugin;
use Awcodes\LightSwitch\Enums\Alignment;
use Cmsmaxinc\FilamentErrorPages\FilamentErrorPagesPlugin;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('5rem')
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Fundacion Odontológica Zoila Padilla')
            ->brandLogo(asset('storage\img\lg_zoila_padilla2.svg'))
            ->brandLogoHeight('3rem')
            ->login()
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => '#ebb619',
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
                RevenueOverviewWidget::class,
                \App\Filament\Widgets\CombinedStatsOverview::class,
                TopProfessionalsChart::class,
                AppointmentsByStatusChart::class,
                TotalAppointmentsChart::class,
                MonthlyRevenueChart::class,


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
            ->passwordReset()
            ->emailVerification()
            ->plugins([
                AuthUIEnhancerPlugin::make()
                ->showEmptyPanelOnMobile(false)
                ->formPanelPosition('right')
                ->formPanelWidth('50%')
                ->emptyPanelBackgroundImageOpacity('70%')
                ->emptyPanelBackgroundColor(Color::hex("#d0b4ca"))
                ->emptyPanelBackgroundImageUrl(asset('storage/img/dentista-examinando-los-dientes-del-paciente-femenino.jpg')),
                FilamentErrorPagesPlugin::make()
                ->routes([
                    'admin/*',
                ]),
                LightSwitchPlugin::make()
                    ->position(Alignment::TopRight),
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
                FilamentApexChartsPlugin::make(),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => Auth::user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ]);;
    }
}
