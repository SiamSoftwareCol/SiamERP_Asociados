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
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Afsakar\FilamentOtpLogin\FilamentOtpLoginPlugin;
use App\Filament\Asociado\Widgets\StatsOverview;
use App\Filament\Asociado\Pages\Auth\LoginAsociado as AuthLoginAsociado;
use App\Filament\Widgets\TableroLogo;
use App\Http\Middleware\isAsociadoMiddleware;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use App\helper\MyImages;

class AsociadoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('asociado')
            ->path('asociado')
            ->login()
            ->passwordReset()
            ->profile(isSimple: false)
            ->maxContentWidth(MaxWidth::ScreenExtraLarge)
            ->font('Roboto')
            ->authGuard('web')
            ->collapsibleNavigationGroups()
            ->sidebarFullyCollapsibleOnDesktop()
            ->brandName('Siam ERP')
            ->brandLogo(asset('images/Icons.png'))
            ->darkModeBrandLogo(asset('images/Icons1.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/Icons.png'))
            ->topNavigation()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Emerald,
                'success' => Color::Red,
                'warning' => Color::Orange,
                'secondary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Asociado/Resources'), for: 'App\\Filament\\Asociado\\Resources')
            ->discoverPages(in: app_path('Filament/Asociado/Pages'), for: 'App\\Filament\\Asociado\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Asociado/Widgets'), for: 'App\\Filament\\Asociado\\Widgets')
            ->widgets([
                StatsOverview::class,
                Widgets\AccountWidget::class,
                TableroLogo::class,
            ])
            ->navigationItems([
                NavigationItem::make('Estado de Cuenta')
                    ->url((fn(): string => route('filament.asociado.resources.estado-cuentas.view', auth()->user()->asociado_id ?? 1)))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->sort(3)
                    ->isActiveWhen(fn() => request()->routeIs('filament.asociado.resources.estado-cuentas.view')),
                NavigationItem::make('Actualizar datos')
                    ->url((fn(): string => route('filament.asociado.resources.perfils.create')))
                    ->icon('heroicon-o-user')
                    ->sort(2)
                    ->isActiveWhen(fn() => request()->routeIs('filament.asociado.resources.perfils.create')),
                NavigationItem::make('dashboard')
                    ->label(fn(): string => __('filament-panels::pages/dashboard.title'))
                    ->url(fn(): string => Dashboard::getUrl())
                    ->isActiveWhen(fn() => request()->routeIs('filament.asociado.pages.dashboard'))
                    ->icon('heroicon-m-home'),
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
                isAsociadoMiddleware::class,
            ])
            ->plugins([
                FilamentOtpLoginPlugin::make()
                        ->loginPage(AuthLoginAsociado::class),
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        MyImages::make()

                    ),
            ])->breadcrumbs(false);
    }
}
