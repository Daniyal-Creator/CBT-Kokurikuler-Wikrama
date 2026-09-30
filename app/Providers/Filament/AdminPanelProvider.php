<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->registration(Register::class)
            ->brandName('Kokurikuler Wikrama')
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandLogoHeight('2.75rem')
            ->colors([
                'primary' => self::hijauBotol(),
                'success' => self::hijauBotol(),
                'gray' => self::abuKertas(),
                'warning' => self::amberJelantah(),
                'danger' => Color::hex('#b3452c'),
                'info' => Color::hex('#3f6f8c'),
            ])
            ->darkMode(false)
            ->topbar(false)
            ->font('Atkinson Hyperlegible Next', provider: LocalFontProvider::class)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => Vite::fonts())
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn () => view('filament.admin.panel-auth'),
                scopes: [Login::class, Register::class],
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Palet panel meminjam benda yang sama dengan halaman publik: kaca botol
     * hijau, kertas daur ulang, dan warna jelantah. Semua warna datar.
     *
     * @return array<int, string>
     */
    private static function hijauBotol(): array
    {
        return [
            50 => 'oklch(0.97 0.018 158)',
            100 => 'oklch(0.94 0.035 158)',
            200 => 'oklch(0.88 0.06 158)',
            300 => 'oklch(0.79 0.09 158)',
            400 => 'oklch(0.66 0.11 158)',
            500 => 'oklch(0.53 0.105 158)',
            600 => 'oklch(0.44 0.09 158)',
            700 => 'oklch(0.36 0.072 158)',
            800 => 'oklch(0.3 0.06 160)',
            900 => 'oklch(0.25 0.045 160)',
            950 => 'oklch(0.18 0.03 160)',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function abuKertas(): array
    {
        return [
            50 => 'oklch(0.958 0.014 88)',
            100 => 'oklch(0.925 0.018 88)',
            200 => 'oklch(0.87 0.02 88)',
            300 => 'oklch(0.8 0.02 90)',
            400 => 'oklch(0.66 0.02 95)',
            500 => 'oklch(0.54 0.02 110)',
            600 => 'oklch(0.44 0.022 130)',
            700 => 'oklch(0.37 0.024 145)',
            800 => 'oklch(0.3 0.026 155)',
            900 => 'oklch(0.25 0.03 158)',
            950 => 'oklch(0.2 0.028 158)',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function amberJelantah(): array
    {
        return [
            50 => 'oklch(0.97 0.03 85)',
            100 => 'oklch(0.94 0.06 84)',
            200 => 'oklch(0.89 0.1 82)',
            300 => 'oklch(0.84 0.13 80)',
            400 => 'oklch(0.8 0.145 78)',
            500 => 'oklch(0.72 0.15 72)',
            600 => 'oklch(0.62 0.14 66)',
            700 => 'oklch(0.52 0.12 62)',
            800 => 'oklch(0.44 0.1 60)',
            900 => 'oklch(0.37 0.08 58)',
            950 => 'oklch(0.26 0.06 56)',
        ];
    }
}
