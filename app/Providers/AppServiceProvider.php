<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Employee;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        App::setLocale('ar');

        Relation::morphMap([
            'employee' => Employee::class,
            'customer' => Customer::class,
            'user' => \App\Models\User::class,
            'invoice' => \App\Models\Invoice::class,
            'transaction' => \App\Models\Transaction::class,
            'incoming_letter' => \App\Models\IncomingLetter::class,
            'outgoing_letter' => \App\Models\OutgoingLetter::class,
            'custody' => \App\Models\Custody::class,
        ]);

        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => '<script>document.documentElement.setAttribute("dir","rtl");document.documentElement.setAttribute("lang","ar");</script>',
        );

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => <<<'HTML'
<style>
    /* === القائمة الجانبية (Sidebar) === */
    .fi-sidebar {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%) !important;
        border-inline-start: 4px solid #3b82f6 !important;
        box-shadow: -4px 0 20px rgba(15, 23, 42, .15);
    }
    .fi-sidebar-header {
        background: rgba(255, 255, 255, .04) !important;
        border-bottom: 1px solid rgba(255, 255, 255, .08) !important;
    }
    .fi-sidebar .fi-sidebar-group-label,
    .fi-sidebar .fi-sidebar-item-label,
    .fi-sidebar .fi-sidebar-header-logo,
    .fi-sidebar .fi-sidebar-nav-groups *,
    .fi-sidebar .fi-topbar-item-label {
        color: #e2e8f0 !important;
    }
    .fi-sidebar .fi-sidebar-group-label {
        color: #94a3b8 !important;
        font-size: 11px !important;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .fi-sidebar .fi-sidebar-item:hover,
    .fi-sidebar .fi-sidebar-item:hover * {
        background: rgba(59, 130, 246, .15) !important;
        color: #fff !important;
    }
    .fi-sidebar .fi-sidebar-item.fi-active,
    .fi-sidebar .fi-sidebar-item.fi-active * {
        background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%) !important;
        color: #fff !important;
        font-weight: 600;
    }
    .fi-sidebar .fi-icon, .fi-sidebar svg {
        color: inherit !important;
    }
    /* فاصل بصري قوي بين القائمة والجسم */
    .fi-main-ctn { background: #f1f5f9 !important; }
    .fi-main { background: #f1f5f9 !important; }
</style>
HTML,
        );
    }
}
