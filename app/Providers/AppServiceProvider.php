<?php

namespace App\Providers;

use App\Models\SaleItem;
use App\Observers\SaleItemObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Keeps Batch::remaining_quantity in sync whenever a sale line is
        // created, edited, or deleted. See app/Observers/SaleItemObserver.php.
        SaleItem::observe(SaleItemObserver::class);    }
}
