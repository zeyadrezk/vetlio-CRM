<?php

namespace App\Providers\Filament;

use App\Http\Middleware\IdentifyTenant;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PublicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('public')
            ->path('')
            ->domain('{subdomain}.' . request()->server('SERVER_NAME'))
            ->maxContentWidth(Width::Full)
            ->topbar(false)
            ->navigation(false)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->viteTheme('resources/css/filament/public/theme.css')
            ->discoverResources(in: app_path('Filament/Public/Resources'), for: 'App\Filament\Public\Resources')
            ->discoverPages(in: app_path('Filament/Public/Pages'), for: 'App\Filament\Public\Pages')
            ->discoverWidgets(in: app_path('Filament/Public/Widgets'), for: 'App\Filament\Public\Widgets')
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
                IdentifyTenant::class
            ]);
    }
}
