<?php

namespace App\Filament\Widgets;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class SalesOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayTotal = Sale::whereDate('created_at', today())->sum('total');
        $weekTotal = Sale::where('created_at', '>=', now()->startOfWeek())->sum('total');

        $lowStockCount = Medicine::query()
            ->withSum('batches as stock_sum', 'remaining_quantity')
            ->get()
            ->filter(fn (Medicine $medicine) => (int) $medicine->stock_sum <= $medicine->alert_threshold)
            ->count();

        $expiringSoonCount = Batch::query()
            ->where('remaining_quantity', '>', 0)
            ->whereBetween('expiry_date', [today(), today()->addDays(90)])
            ->count();

        return [
            Stat::make(__('pharmacy.dashboard.today_sales'), number_format($todayTotal, 2).' $')
                ->description(__('pharmacy.dashboard.today_sales_desc'))
                ->color('success'),

            Stat::make(__('pharmacy.dashboard.this_week'), number_format($weekTotal, 2).' $')
                ->description(__('pharmacy.dashboard.this_week_desc'))
                ->color('success'),

            Stat::make(__('pharmacy.dashboard.low_stock_medicines'), $lowStockCount)
                ->description(__('pharmacy.dashboard.low_stock_desc'))
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make(__('pharmacy.dashboard.expiring_soon_stat'), $expiringSoonCount)
                ->description(__('pharmacy.dashboard.expiring_soon_desc'))
                ->color($expiringSoonCount > 0 ? 'warning' : 'success'),
        ];
    }
}