<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CountryResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\VisaTypeResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
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
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use App\Filament\Pages\Product;
use App\Filament\Resources\DashboardResource;
use App\Filament\Resources\GuestResource\RegisterHandler;
use App\Models\User;
use Filament\Navigation\MenuItem;
use Illuminate\Support\Facades\Gate;
use App\Filament\Resources\ProfileResource\EditProfile;
use App\Models\Setting;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $setting = Setting::first(['site_title', 'site_logo', 'favicon']);
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->sidebarCollapsibleOnDesktop()
            ->login()
            //->brandName('') // we can cutomize this, otherwise taken from .env
            ->brandLogo($setting->site_logo_url)
            // ->brandLogoHeight('2rem') // optional
            ->favicon($setting->favicon_url)
            ->registration(RegisterHandler::class)
            ->passwordReset()
           ->emailVerification()
            ->profile(
                EditProfile::class,
                isSimple: false
            )
            // ->topbar(false)
            //->topNavigation()
            ->collapsibleNavigationGroups(true)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->label(fn (): string => auth()->user()->name),
                // 'profile' => MenuItem::make()->label("Edit Profile"),
                MenuItem::make()
                ->label('Products')
                ->icon('heroicon-o-cube')
                ->visible(fn (): bool => auth()->user()->can('View product'))
                // ->url(fn (): string => route('filament.admin.resources.products.index'))
                ->url(fn (): string => ProductResource::getUrl())
                // ...
            ])
            // ->navigationGroups([
            //     'Inventory Management',
            //     'Visa',
            //     'Customer Support',
            //     'Financial Management',
            //     'Site Management',
            //     'User Management',
            // ])
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Inventory Management'),
                NavigationGroup::make()
                    ->label('Visa'),
                NavigationGroup::make()
                    ->label('Customer Support'),
                NavigationGroup::make()
                    ->label('Financial Management'),
                NavigationGroup::make()
                    ->label('Site Management'),
                NavigationGroup::make()
                    ->label('User Management'),
            ])
            // ->navigationItems([
            //     NavigationItem::make('Products')
            //     ->url(fn (): string => ProductResource::getUrl())
            //         ->icon('heroicon-o-cube')
            //          ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.products.index')),
            //         ->sort(3),
            // ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                DashboardResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                // VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
