<?php

namespace App\Providers;

use App\Models\SaleItem;
use App\Observers\SaleItemObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        SaleItem::observe(SaleItemObserver::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(['ar', 'fr', 'en']);
        });
    }
}